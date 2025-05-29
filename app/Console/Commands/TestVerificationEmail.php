<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestVerificationEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:verification-email {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test verification email sending functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'test-' . Str::random(5) . '@example.com';
        
        $this->info("Testing verification email with address: {$email}");
        
        // Check if user already exists
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            // Create a test user
            $user = User::create([
                'id' => (string) Str::uuid(),
                'name' => 'Test User',
                'email' => $email,
                'password' => Hash::make('password123'),
                'role' => 20,
            ]);
            
            $this->info("Created test user with ID: {$user->id}");
        } else {
            $this->info("Using existing user with ID: {$user->id}");
        }
        
        // Send verification email
        $user->sendEmailVerificationNotification();
        
        $this->info("Verification email queued for sending!");
        $this->info("Check your Mailtrap inbox at https://mailtrap.io to see the email.");
        $this->info("If you don't see the email, make sure your queue is running or set QUEUE_CONNECTION=sync in your .env file.");
        
        return Command::SUCCESS;
    }
}
