<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\PermissionHelper;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
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

            // Debug logging
            \Log::info('Permission check', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'permission' => $permission,
                'user_permissions' => $user->permissions->pluck('name')->toArray(),
                'has_permission' => $user->hasPermissionTo($permission)
            ]);

            // Check permission using custom helper
            // if (!_has_permission($user, $permission)) {
            if (!PermissionHelper::hasPermission($user, $permission)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => _trans('permissions.access_denied')
                    ], 403);
                }
                
                return redirect()->back()->with('error', _trans('permissions.access_denied'));
            }

            return $next($request);

        } catch (\Exception $e) {
            _log_error('Permission middleware failed', [
                'permission' => $permission,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'url' => $request->fullUrl()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('permissions.middleware_error')
                ], 500);
            }

            return redirect()->back()->with('error', _trans('permissions.middleware_error'));
        }
    }
}
