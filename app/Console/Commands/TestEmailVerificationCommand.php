<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestEmailVerificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-verification {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email verification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing email verification system for: {$email}");
        
        try {
            // Create a test user
            $verificationToken = Str::random(64);
            
            $user = User::create([
                'username' => 'testuser_' . time(),
                'name' => 'Test User',
                'full_name' => 'Test User',
                'email' => $email,
                'password' => Hash::make('password123'),
                'phone' => '08123456789',
                'department' => 'IT',
                'role' => 'user',
                'email_verified_at' => null,
                'email_verification_token' => Hash::make($verificationToken),
            ]);

            // Refresh user to get the actual data from database
            $user = $user->fresh();

            $this->info("✅ Test user created with ID: {$user->id}");

            // Send verification email
            $verificationUrl = route('email.verify', ['token' => $verificationToken]);
            Mail::to($email)->send(new EmailVerificationMail($user, $verificationUrl));

            $this->info("✅ Email verification sent successfully!");
            $this->info("🔗 Verification URL: {$verificationUrl}");
            
            // Test verification
            $this->info("Testing email verification...");
            
            // Debug: Show token info
            $this->info("🔍 Debug info:");
            $this->info("   Original token: " . $verificationToken);
            $this->info("   Stored token hash: " . ($user->email_verification_token ?? 'NULL'));
            $this->info("   User attributes: " . json_encode($user->getAttributes()));
            $this->info("   Hash check result: " . (Hash::check($verificationToken, $user->email_verification_token) ? 'true' : 'false'));
            
            // Find user by token using the same method as controller
            $userToVerify = User::whereNotNull('email_verification_token')
                ->get()
                ->first(function ($user) use ($verificationToken) {
                    return Hash::check($verificationToken, $user->email_verification_token);
                });
            
            if ($userToVerify) {
                $userToVerify->update([
                    'email_verified_at' => now(),
                    'email_verification_token' => null,
                ]);
                
                $this->info("✅ Email verification successful!");
                $this->info("✅ User email verified at: " . $userToVerify->email_verified_at);
            } else {
                $this->error("❌ Email verification failed!");
                $this->error("❌ No user found with matching token");
            }
            
            // Clean up test user
            $user->delete();
            $this->info("🧹 Test user cleaned up");
            
            $this->info("🎉 Email verification system test completed successfully!");
            
        } catch (\Exception $e) {
            $this->error("❌ Email verification test failed: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
        }
    }
}
