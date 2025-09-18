<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Change the application language
     */
    public function changeLanguage(Request $request, $locale)
    {
        try {
            // Validate locale
            if (!in_array($locale, ['en', 'es'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language selected'
                ], 400);
            }

            // Set the application locale
            App::setLocale($locale);
            
            // Store in session
            Session::put('locale', $locale);
            
            // Update user preference if authenticated
            if (auth()->check()) {
                auth()->user()->update(['language_preference' => $locale]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Language changed successfully',
                'locale' => $locale
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to change language', [
                'error' => $e->getMessage(),
                'locale' => $locale,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to change language'
            ], 500);
        }
    }

    /**
     * Get current language
     */
    public function getCurrentLanguage()
    {
        return response()->json([
            'success' => true,
            'locale' => App::getLocale()
        ]);
    }
}