<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;

class RateLimitUploads
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next)
    {
        $key = 'upload_' . $request->ip();
        
        if ($this->limiter->tooManyAttempts($key, 5)) { // 5 uploads per hour
            $seconds = $this->limiter->availableIn($key);
            return response()->json([
                'message' => 'Too many upload attempts. Try again in ' . ceil($seconds / 60) . ' minutes.'
            ], 429);
        }

        $this->limiter->hit($key, 3600); // 1 hour window

        return $next($request);
    }
}