<?php

namespace Subfission\Cas\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use Subfission\Cas\CasManager;
use Subfission\Cas\Middleware\CASAuth;
use Subfission\Cas\Middleware\RedirectCASAuthenticated;

class CasMiddlewareTest extends PackageTestCase
{
    protected function defineRoutes($router): void
    {
        $router->get('/protected', function () {
            return 'ok:'.session('cas_user');
        })->middleware(CASAuth::class);

        $router->get('/guest', function () {
            return 'guest';
        })->middleware(RedirectCASAuthenticated::class);
    }

    public function testAuthenticatedRequestStoresTheCasUser(): void
    {
        $cas = $this->bindCas();
        $cas->expects($this->once())->method('checkAuthentication')->willReturn(true);
        $cas->expects($this->once())->method('user')->willReturn('alice');
        $cas->expects($this->never())->method('authenticate');

        $response = $this->get('/protected');

        $response->assertOk();
        $response->assertSee('ok:alice');
        $response->assertSessionHas('cas_user', 'alice');
    }

    public function testJsonRequestWithoutASessionReturnsUnauthorized(): void
    {
        $cas = $this->bindCas();
        $cas->expects($this->once())->method('checkAuthentication')->willReturn(false);
        $cas->expects($this->never())->method('authenticate');

        $this->getJson('/protected')
            ->assertStatus(401)
            ->assertSee('Unauthorized.');
    }

    public function testAjaxRequestWithoutASessionReturnsUnauthorized(): void
    {
        $cas = $this->bindCas();
        $cas->expects($this->once())->method('checkAuthentication')->willReturn(false);
        $cas->expects($this->never())->method('authenticate');

        $this->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->get('/protected')
            ->assertStatus(401);
    }

    public function testBrowserRequestWithoutASessionStartsAuthentication(): void
    {
        $cas = $this->bindCas();
        $cas->expects($this->once())->method('checkAuthentication')->willReturn(false);
        $cas->expects($this->once())->method('authenticate')->willReturn(true);

        $this->get('/protected')
            ->assertOk()
            ->assertSee('ok:');
    }

    public function testGuestMiddlewareAllowsAnonymousVisitors(): void
    {
        $cas = $this->bindCas();
        $cas->expects($this->once())->method('checkAuthentication')->willReturn(false);

        $this->get('/guest')
            ->assertOk()
            ->assertSee('guest');
    }

    public function testGuestMiddlewareRedirectsAuthenticatedVisitors(): void
    {
        config(['cas.cas_redirect_path' => '/dashboard']);

        $cas = $this->bindCas();
        $cas->expects($this->once())->method('checkAuthentication')->willReturn(true);

        $this->get('/guest')->assertRedirect('/dashboard');
    }

    /**
     * @return CasManager&MockObject
     */
    private function bindCas(): CasManager
    {
        /** @var CasManager&MockObject $cas */
        $cas = $this->createMock(CasManager::class);
        $this->app->instance('cas', $cas);

        return $cas;
    }
}
