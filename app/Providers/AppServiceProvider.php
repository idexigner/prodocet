<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(
            \App\Interfaces\CourseInterface::class,
            \App\Repositories\CourseRepository::class
        );
        
        $this->app->bind(
            \App\Interfaces\GroupInterface::class,
            \App\Repositories\GroupRepository::class
        );
        
        $this->app->bind(
            \App\Interfaces\CurriculumInterface::class,
            \App\Repositories\CurriculumRepository::class
        );

        // Existing module repository bindings
        $this->app->bind(
            \App\Interfaces\StudentInterface::class,
            \App\Repositories\StudentRepository::class
        );
        
        $this->app->bind(
            \App\Interfaces\TeacherInterface::class,
            \App\Repositories\TeacherRepository::class
        );
        
        $this->app->bind(
            \App\Interfaces\RoleInterface::class,
            \App\Repositories\RoleRepository::class
        );

        // Settings repository bindings
        $this->app->bind(
            \App\Interfaces\RateSchemeInterface::class,
            \App\Repositories\RateSchemeRepository::class
        );
        
        $this->app->bind(
            \App\Interfaces\LanguageInterface::class,
            \App\Repositories\LanguageRepository::class
        );
        
        $this->app->bind(
            \App\Interfaces\CourseLevelInterface::class,
            \App\Repositories\CourseLevelRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
