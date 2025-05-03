<?php

namespace Aliziodev\GmailUnique\Console;

use Illuminate\Console\Command;

/**
 * Installation command for Laravel Gmail Unique package.
 *
 * @package Aliziodev\GmailUnique\Console
 */
class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gmail-unique:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Laravel Gmail Unique package';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Installing Laravel Gmail Unique...');

        // Publish configuration
        $this->publishConfiguration();
        
        // Show usage information
        $this->showUsageInformation();

        $this->info('Installation completed!');
    }

    /**
     * Publish the configuration file.
     */
    protected function publishConfiguration(): void
    {
        $this->info('Publishing configuration...');

        $this->callSilently('vendor:publish', [
            '--tag' => 'gmail-unique',
            '--force' => true,
        ]);

        $this->line('  Configuration file published successfully.');
    }
    
    /**
     * Show usage information.
     */
    protected function showUsageInformation(): void
    {
        $this->info('How to use Laravel Gmail Unique:');
        $this->line('1. Add the HasNormalizedEmail trait to your User model:');
        $this->line('   use Aliziodev\\GmailUnique\\Traits\\HasNormalizedEmail;');
        $this->line('2. That\'s it! Your Gmail emails will now be normalized during validation.');
        $this->line('   This prevents duplicate accounts with email variations like:');
        $this->line('     - user@gmail.com');
        $this->line('     - u.s.e.r@gmail.com');
        $this->line('     - user+alias@gmail.com');
    }
}