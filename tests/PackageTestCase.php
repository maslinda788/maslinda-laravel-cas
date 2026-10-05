<?php

namespace Subfission\Cas\Tests;

use Orchestra\Testbench\TestCase;
use Subfission\Cas\CasServiceProvider;
use Subfission\Cas\Facades\Cas;

abstract class PackageTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            CasServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Cas' => Cas::class,
        ];
    }
}
