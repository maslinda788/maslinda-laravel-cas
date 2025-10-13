<?php

namespace Subfission\Cas;

use Illuminate\Support\ServiceProvider;
use Illuminate\Log\LogManager; // gunakan LogManager, bukan LogFactory

class CasServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     */
    protected bool $defer = false;

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gunakan tag publish yang jelas untuk Laravel 12
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('cas.php'),
        ], 'cas-config');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge config supaya setting user override default
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/config.php',
            'cas'
        );

        // Bind singleton 'cas' ke container
        $this->app->singleton('cas', function ($app) {
            $cas = new CasManager(config('cas'));

            /** @var LogManager $logger */
            $logger = $app->make(LogManager::class);

            $cas->setLogger($logger->channel('stack'));

            return $cas;
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return ['cas'];
    }
}
