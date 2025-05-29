<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;

class TestMailConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mail-connection {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test mail connection by sending a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Make sure we're using sync queue for immediate sending
        $this->updateEnvVariable('QUEUE_CONNECTION', 'sync');
        
        // Make sure MAIL_MAILER is set to smtp
        $this->updateEnvVariable('MAIL_MAILER', 'smtp');
        
        // Clear config cache to apply .env changes
        $this->info('Clearing configuration cache...');
        Artisan::call('config:clear');
        
        // Display current mail configuration
        $this->info('Current mail configuration:');
        $this->info('MAIL_MAILER: ' . config('mail.mailer'));
        $this->info('MAIL_HOST: ' . config('mail.mailers.smtp.host'));
        $this->info('MAIL_PORT: ' . config('mail.mailers.smtp.port'));
        $this->info('MAIL_USERNAME: ' . config('mail.mailers.smtp.username'));
        $this->info('MAIL_FROM_ADDRESS: ' . config('mail.from.address'));
        
        $email = $this->argument('email') ?? 'test@example.com';
        
        $this->info("Sending test email to: {$email}");
        
        try {
            Mail::raw('This is a test email from RDV DISCOS.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email from RDV DISCOS');
            });
            
            $this->info('Test email sent successfully! Check your Mailtrap inbox.');
            $this->info('If the email does not appear in Mailtrap, check your Mailtrap credentials.');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error sending test email: ' . $e->getMessage());
            
            // Provide troubleshooting guidance based on the error
            if (str_contains($e->getMessage(), 'Connection could not be established')) {
                $this->warn('Connection issue detected. Please check:');
                $this->warn('1. Your Mailtrap credentials are correct');
                $this->warn('2. Your internet connection is working');
                $this->warn('3. Mailtrap service is online');
            }
            
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
