<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ConfigureMailForTesting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'configure:mail-testing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configure mail settings for immediate testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Set queue connection to sync for immediate processing
        $this->updateEnvVariable('QUEUE_CONNECTION', 'sync');
        
        // Test email configurations
        $this->info('Testing mail configuration...');
        
        try {
            // Clear configuration cache
            $this->info('Clearing configuration cache...');
            Artisan::call('config:clear');
            
            // Display current mail configuration
            $this->info('Current mail configuration:');
            $this->info('MAIL_MAILER: ' . config('mail.mailer'));
            $this->info('MAIL_HOST: ' . config('mail.mailers.smtp.host'));
            $this->info('MAIL_PORT: ' . config('mail.mailers.smtp.port'));
            $this->info('MAIL_USERNAME: ' . config('mail.mailers.smtp.username'));
            $this->info('MAIL_FROM_ADDRESS: ' . config('mail.from.address'));
            
            $this->info('Mail configuration has been updated for testing.');
            $this->info('Now try running: php artisan test:verification-email');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error testing mail configuration: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Update .env variable
     *
     * @param string $key
     * @param string $value
     * @return bool
     */
    protected function updateEnvVariable($key, $value)
    {
        $path = app()->environmentFilePath();
        
        if (file_exists($path)) {
            $content = file_get_contents($path);
            
            // If the key exists, replace its value
            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
            } else {
                // If the key doesn't exist, add it
                $content .= "\n{$key}={$value}\n";
            }
            
            file_put_contents($path, $content);
            
            $this->info("Updated {$key} to {$value} in .env file");
            return true;
        }
        
        $this->error('.env file not found');
        return false;
    }
}
