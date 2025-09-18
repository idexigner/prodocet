<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        try {
            // Check if user is authenticated
            if (!Auth::check()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => _trans('auth.user_not_authenticated')
                    ], 401);
                }
                
                return redirect()->route('login');
            }

            $user = Auth::user();

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => _trans('auth.account_inactive')
                    ], 403);
                }
                
                return redirect()->route('login')->with('error', _trans('auth.account_inactive'));
            }

            // Check role
            if (!$user->hasRole($role)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => _trans('roles.access_denied')
                    ], 403);
                }
                
                return redirect()->back()->with('error', _trans('roles.access_denied'));
            }

            return $next($request);

        } catch (\Exception $e) {
            _log_error('Role middleware failed', [
                'role' => $role,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'url' => $request->fullUrl()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('roles.middleware_error')
                ], 500);
            }

            return redirect()->back()->with('error', _trans('roles.middleware_error'));
        }
    }
}
