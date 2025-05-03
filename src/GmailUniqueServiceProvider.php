<?php

namespace Aliziodev\GmailUnique;

use Illuminate\Support\ServiceProvider;
use Aliziodev\GmailUnique\Services\GmailUniqueService;

/**
 * Service provider for Gmail Unique email normalization package.
 */
class GmailUniqueServiceProvider extends ServiceProvider
{

    /**
     * The commands to be registered.
     *
     * @var array<class-string>
     */
    protected $commands = [
        Console\InstallCommand::class,
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/gmail-unique.php' => config_path('gmail-unique.php'),
            ], 'gmail-unique');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/gmail-unique.php',
            'gmail-unique'
        );

        $this->app->singleton(GmailUniqueService::class, function ($app) {
            $config = $app['config']->get('gmail-unique');
            return new GmailUniqueService(
                $config['domains'] ?? null,
                $config['email_column'] ?? null
            );
        });

        // Register the commands
        $this->commands($this->commands);
    }
}
