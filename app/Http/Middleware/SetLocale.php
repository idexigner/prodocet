<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from session or user preference
        $locale = Session::get('locale');
        
        if (!$locale && auth()->check()) {
            $locale = auth()->user()->language_preference ?? 'es';
        }
        
        // Default to Spanish if no locale is set
        $locale = $locale ?? 'es';
        
        // Set the application locale
        App::setLocale($locale);
        
        return $next($request);
    }
}
