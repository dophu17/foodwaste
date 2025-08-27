<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Closure): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get locale from session or use default
        $locale = session()->get('locale', 'ja');
        
        // Set the application locale
        App::setLocale($locale);
        
        // Debug: Log the current locale
        // \Log::info('Current locale set to: ' . $locale);
        // \Log::info('App locale: ' . App::getLocale());
        
        return $next($request);
    }
}

