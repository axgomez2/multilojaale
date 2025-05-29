<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class FixMailConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:mail-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix mail configuration issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing mail configuration...');
        
        // Essential mail configurations
        $envUpdates = [
            'QUEUE_CONNECTION' => 'sync',
            'MAIL_MAILER' => 'smtp',
            'MAIL_HOST' => 'smtp.mailtrap.io',
            'MAIL_PORT' => '2525',
            'MAIL_USERNAME' => 'ede1d66ddc703b',
            'MAIL_PASSWORD' => '5d757e21105b53',
            'MAIL_ENCRYPTION' => 'tls',
            'MAIL_FROM_ADDRESS' => '2a6e30622a-50b13f@inbox.mailtrap.io',
            'MAIL_FROM_NAME' => 'RDV DISCOS',
        ];
        
        // Update all env variables
        foreach ($envUpdates as $key => $value) {
            $this->updateEnvVariable($key, $value);
        }
        
        // Clear all caches to ensure configurations are fresh
        $this->info('Clearing application caches...');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        
        $this->info('Mail configuration fixed!');
        $this->info('Now try running: php artisan test:verification-email');
        
        return Command::SUCCESS;
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
