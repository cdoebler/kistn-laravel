<?php

declare(strict_types=1);

namespace Kistn\Laravel;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Kistn\Cache\LocalHashCache;
use Kistn\Client\InventoryClient;
use Kistn\Collector\ComposerCollector;
use Kistn\Collector\NpmCollector;
use Kistn\InventoryPusher;
use Kistn\Laravel\Console\PushCommand;
use Kistn\Process\ShellProcessRunner;
use Kistn\TransmitMode;

class ServiceProvider extends BaseServiceProvider
{
    #[\Override]
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/kistn.php', 'kistn');

        $this->app->singleton(InventoryPusher::class, function (): InventoryPusher {
            /** @var \Illuminate\Config\Repository $repository */
            $repository = $this->app->make('config');

            /** @var array{base_url: string, project_id: string, token: string, cache_path: string, work_dir: string, transmit_composer_files: string, transmit_npm_files: string} $config */
            $config = $repository->get('kistn');

            $factory = new HttpFactory();
            $client = new InventoryClient(
                baseUrl: $config['base_url'],
                projectId: $config['project_id'],
                token: $config['token'],
                httpClient: new GuzzleClient([
                    'timeout'         => 10,
                    'connect_timeout' => 5,
                ]),
                requestFactory: $factory,
                streamFactory: $factory,
            );

            $runner = new ShellProcessRunner();

            $collectors = [
                new ComposerCollector(
                    lockFilePath: $config['work_dir'] . '/composer.lock',
                    composerJsonPath: $config['work_dir'] . '/composer.json',
                    runner: $runner,
                    installedJsonPath: $config['work_dir'] . '/vendor/composer/installed.json',
                ),
                new NpmCollector(
                    lockFilePath: $config['work_dir'] . '/package-lock.json',
                    packageJsonPath: $config['work_dir'] . '/package.json',
                    runner: $runner,
                ),
            ];

            return new InventoryPusher(
                client: $client,
                collectors: $collectors,
                cache: new LocalHashCache($config['cache_path']),
                transmitComposerFiles: TransmitMode::tryFrom($config['transmit_composer_files']) ?? TransmitMode::Never,
                transmitNpmFiles: TransmitMode::tryFrom($config['transmit_npm_files']) ?? TransmitMode::Never,
            );
        });

        $this->commands([PushCommand::class]);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/kistn.php' => config_path('kistn.php'),
            ], 'kistn-config');
        }
    }
}
