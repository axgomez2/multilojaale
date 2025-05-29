<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class DirectEmailTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:direct-test {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending by bypassing .env configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'test@example.com';
        
        $this->info("Setting up mail configuration directly in code...");
        
        // Override mail configuration directly
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', 'smtp.mailtrap.io');
        Config::set('mail.mailers.smtp.port', 2525);
        Config::set('mail.mailers.smtp.encryption', 'tls');
        Config::set('mail.mailers.smtp.username', 'ede1d66ddc703b');
        Config::set('mail.mailers.smtp.password', '5d757e21105b53');
        Config::set('mail.from.address', '2a6e30622a-50b13f@inbox.mailtrap.io');
        Config::set('mail.from.name', 'RDV DISCOS');
        
        $this->info("Sending test email to: {$email}");
        
        try {
            Mail::raw('This is a direct test email from RDV DISCOS.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Direct Test Email from RDV DISCOS');
            });
            
            $this->info('Test email sent successfully! Check your Mailtrap inbox.');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error sending test email: ' . $e->getMessage());
            
            // Display detailed debug information
            $this->warn('Mail Configuration:');
            $this->warn('Driver: ' . Config::get('mail.default'));
            $this->warn('Host: ' . Config::get('mail.mailers.smtp.host'));
            $this->warn('Port: ' . Config::get('mail.mailers.smtp.port'));
            $this->warn('Username: ' . Config::get('mail.mailers.smtp.username'));
            $this->warn('From Address: ' . Config::get('mail.from.address'));
            
            return Command::FAILURE;
        }
    }
}
