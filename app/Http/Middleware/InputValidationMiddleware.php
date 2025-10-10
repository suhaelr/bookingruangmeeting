<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InputValidationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Sanitize all input data
        $input = $request->all();
        $sanitized = $this->sanitizeInput($input);
        $request->replace($sanitized);
        
        return $next($request);
    }
    
    private function sanitizeInput($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        
        if (is_string($data)) {
            // Remove potential SQL injection patterns
            $data = preg_replace('/(\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|UNION|SCRIPT)\b)/i', '', $data);
            // Remove potential XSS patterns
            $data = strip_tags($data);
            // Escape special characters
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
        
        return $data;
    }
}