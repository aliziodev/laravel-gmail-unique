<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Aliziodev\GmailUnique\GmailUniqueServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    
    protected function getPackageProviders($app)
    {
        return [
            GmailUniqueServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadLaravelMigrations();
        $this->app['config']->set('gmail-unique.domains', ['gmail.com', 'googlemail.com']);
        $this->app['config']->set('gmail-unique.email_column', 'email');
    }
    
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database for testing
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}