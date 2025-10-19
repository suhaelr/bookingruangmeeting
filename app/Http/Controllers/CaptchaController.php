<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class CaptchaController extends Controller
{
    /**
     * Generate a simple math captcha
     */
    public function generate()
    {
        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        $operation = rand(0, 1) ? '+' : '-';
        
        if ($operation === '+') {
            $answer = $num1 + $num2;
            $question = "$num1 + $num2 = ?";
        } else {
            // Ensure result is positive
            if ($num1 < $num2) {
                $temp = $num1;
                $num1 = $num2;
                $num2 = $temp;
            }
            $answer = $num1 - $num2;
            $question = "$num1 - $num2 = ?";
        }
        
        // Store the answer in session
        Session::put('captcha_answer', $answer);
        Session::put('captcha_question', $question);
        
        return response()->json([
            'question' => $question,
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
