<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;
use App\Models\User;

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
    protected $description = 'Test email sending functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing email to: {$email}");
        
        try {
            // Test 1: Simple text email
            $this->info("Sending simple text email...");
            Mail::raw('This is a test email from Meeting Room Booking System.', function($message) use ($email) {
                $message->to($email)->subject('Test Email - Simple Text');
            });
            $this->info("✅ Simple text email sent successfully!");
            
            // Test 2: Welcome email (if user exists)
            $user = User::where('email', $email)->first();
            if ($user) {
                $this->info("Sending welcome email...");
                Mail::to($email)->send(new WelcomeEmail($user));
                $this->info("✅ Welcome email sent successfully!");
            } else {
                $this->warn("⚠️ User not found, skipping welcome email test");
            }
            
            $this->info("🎉 All email tests completed successfully!");
            
        } catch (\Exception $e) {
            $this->error("❌ Email test failed: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
        }
    }
}