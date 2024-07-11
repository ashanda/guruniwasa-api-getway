<?php

namespace App\Http\Middleware;

use App\Models\AuthToken;
use Illuminate\Http\Request;
use Closure;

class AuthTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    
      public function handle(Request $request, Closure $next)
    {
        // Get the bearer token from the request
        $token = $request->bearerToken();

        // Validate the token
        if (!$this->validateToken($token)) {
            return response()->json(['status' => false, 'message' => 'Invalid token'], 401);
        }

        // Add the token to the request attributes for further usage
        $request->attributes->set('token', $token);

        return $next($request);
    }

     protected function validateToken($token)
    {
        // Assuming you have a method to validate the token
        return AuthToken::where('access_token', $token)->exists();
    }
    
}
