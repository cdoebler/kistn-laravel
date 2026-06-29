<?php

namespace Kistn\Laravel\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Kistn\Laravel\ServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    /** @return class-string[] */
    protected function getPackageProviders($app): array
    {
        return [ServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('kistn.base_url', 'https://example.com');
        $app['config']->set('kistn.project_id', 'test-uuid');
        $app['config']->set('kistn.token', 'test-token');
        $app['config']->set('kistn.cache_path', sys_get_temp_dir() . '/.test-inventory.hash');
        $app['config']->set('kistn.work_dir', sys_get_temp_dir());
    }
}
