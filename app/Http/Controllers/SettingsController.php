<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display the settings dashboard
     */
    public function index()
    {
        $rateSchemesCount = \App\Models\RateScheme::count();
        $languagesCount = \App\Models\Language::count();
        $courseLevelsCount = \App\Models\CourseLevel::count();

        return view('settings.index', compact('rateSchemesCount', 'languagesCount', 'courseLevelsCount'));
    }
}
