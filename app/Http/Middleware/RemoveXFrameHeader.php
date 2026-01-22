<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RemoveXFrameHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        if ($response instanceof Response && method_exists($response, 'headers')) {
            $response->headers->remove('X-Frame-Options');
        }
        
        return $response;
    }
}
