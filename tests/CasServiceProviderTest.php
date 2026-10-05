<?php

namespace Subfission\Cas\Tests;

use Illuminate\Foundation\Application;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use ReflectionProperty;
use Subfission\Cas\CasManager;
use Subfission\Cas\Facades\Cas;

class CasServiceProviderTest extends PackageTestCase
{
    public function testRunsOnLaravel12Or13(): void
    {
        $major = (int) Application::VERSION;

        $this->assertContains($major, [12, 13]);
    }

    public function testMergesDefaultCasConfig(): void
    {
        $this->assertSame('cas.myuniv.edu', config('cas.cas_hostname'));
        $this->assertSame('/cas', config('cas.cas_uri'));
        $this->assertSame('2.0', config('cas.cas_version'));
        $this->assertTrue($this->app->bound('cas'));
    }

    public function testFacadeAndHelperResolveTheContainerBinding(): void
    {
        /** @var CasManager&MockObject $cas */
        $cas = $this->createMock(CasManager::class);
        $cas->expects($this->once())->method('isAuthenticated')->willReturn(true);

        $this->app->instance('cas', $cas);
        Cas::clearResolvedInstance('cas');

        $this->assertTrue(Cas::isAuthenticated());
        $this->assertSame($cas, cas());
    }

    public function testResolvesCasManagerWithTheLaravelLogger(): void
    {
        $manager = $this->app->make('cas');
        $logger = (new ReflectionProperty($manager, 'logger'))->getValue($manager);

        $this->assertInstanceOf(CasManager::class, $manager);
        $this->assertInstanceOf(LoggerInterface::class, $logger);
        $this->assertSame('cas.myuniv.edu', $manager->getConfig()['cas_hostname']);
    }

    public function testPublishesCasConfig(): void
    {
        $target = config_path('cas.php');

        if (is_file($target)) {
            unlink($target);
        }

        $this->artisan('vendor:publish', [
            '--tag' => 'cas-config',
            '--force' => true,
        ])->assertSuccessful();

        $this->assertFileExists($target);

        $published = file_get_contents($target);
        $this->assertIsString($published);
        $this->assertStringContainsString('cas_hostname', $published);
    }
}
