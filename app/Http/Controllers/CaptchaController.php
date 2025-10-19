<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class CaptchaController extends Controller
{
    /**
     * Generate a simple number captcha
     */
    public function generate()
    {
        // Generate 4-digit number (1000-9999)
        $answer = rand(1000, 9999);
        $question = "Masukkan angka: " . $answer;
        
        // Store the answer in session
        Session::put('captcha_answer', $answer);
        Session::put('captcha_question', $question);
        
        return response()->json([
            'question' => $answer, // Return only the number
            'answer' => $answer // For debugging only, remove in production
        ]);
    }
    
    /**
     * Verify captcha answer
     */
    public function verify(Request $request)
    {
        $userAnswer = $request->input('captcha_answer');
        $correctAnswer = Session::get('captcha_answer');
        
        if ($userAnswer == $correctAnswer) {
            Session::put('captcha_verified', true);
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'message' => 'Captcha salah']);
    }
}
