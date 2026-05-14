<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isLocalhost = in_array($request->getHost(), ['localhost', '127.0.0.1']);
        
        if (!$request->secure() && !$isLocalhost) {
            // Kita bisa redirect atau abort. Untuk "memblokir", abort dengan pesan.
            return response()->view('errors.https-required', [], 403);
        }

        return $next($request);
    }
}
