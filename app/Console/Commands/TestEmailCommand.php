<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\NewUserNotification;
use Illuminate\Console\Command;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending email notification to a user via Mailgun';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        // Create a temporary user for testing
        $user = new User();
        $user->email = $email;
        $user->name = 'Test User';
        
        try {
            $this->info("Sending email to: {$email}");
            $this->info("Using Mailgun configuration...");
            
            $user->notify(new NewUserNotification());
            
            $this->info("✅ Email notification sent successfully to {$email}");
            $this->info("📧 Check your email inbox (and spam folder) for the message");
            $this->info("📊 Check Mailgun dashboard for delivery status");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to send email: " . $e->getMessage());
            $this->warn("💡 Make sure the email address is authorized in your Mailgun sandbox");
            $this->warn("💡 Check your Mailgun credentials and domain configuration");
        }
    }
}