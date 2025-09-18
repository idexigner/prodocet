<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Throwable;

trait ErrorLogging
{
    public function logException(Throwable $e, $routeName, $methodName, $isWeb = false)
    {
        

        DB::table('error_logs')->insert([
            'route_name' => $routeName,
            'method_name' => $methodName,
            'message' => $e->getMessage(),
            'is_web' => $isWeb,
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'stack_trace' => $e->getTraceAsString(),
            'user_id' => Auth::check() ? Auth::id() : null, // Will be null if user is not authenticated
            'created_at' => now()
        ]);
    }
} 