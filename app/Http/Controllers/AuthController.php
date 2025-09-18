<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Interfaces\UserInterface;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected UserInterface $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle user login
     */
    public function login(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('auth.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find user by email
            $user = $this->userRepository->getUserByEmail($request->email);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('auth.user_not_found')
                ], 404);
            }

            // Check if user is active
            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('auth.account_inactive')
                ], 403);
            }

            // Verify password
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('auth.invalid_credentials')
                ], 401);
            }

            // Create token
            $token = $user->createToken('auth-token')->plainTextToken;

            // Update last login
            $this->userRepository->updateLastLogin($user->id);

            // Log successful login
            Log::info('User logged in successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('auth.login_successful'),
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                    'permissions' => _get_user_permissions($user),
                    'role' => _get_user_role()
                ]
            ]);

        } catch (\Exception $e) {
            _log_error('Login failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('auth.login_failed')
            ], 500);
        }
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if ($user) {
                // Revoke current token
                $user->currentAccessToken()->delete();

                // Log logout
                Log::info('User logged out', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $request->ip()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => _trans('auth.logout_successful')
            ]);

        } catch (\Exception $e) {
            _log_error('Logout failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('auth.logout_failed')
            ], 500);
        }
    }

    /**
     * Get current user info
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('auth.user_not_authenticated')
                ], 401);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => new UserResource($user),
                    'permissions' => _get_user_permissions($user),
                    'role' => _get_user_role()
                ]
            ]);

        } catch (\Exception $e) {
            _log_error('Get user info failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('auth.get_user_info_failed')
            ], 500);
        }
    }

    /**
     * Refresh user token
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('auth.user_not_authenticated')
                ], 401);
            }

            // Revoke current token
            $user->currentAccessToken()->delete();

            // Create new token
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => _trans('auth.token_refreshed'),
                'data' => [
                    'token' => $token
                ]
            ]);

        } catch (\Exception $e) {
            _log_error('Token refresh failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('auth.token_refresh_failed')
            ], 500);
        }
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('auth.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('auth.current_password_incorrect')
                ], 400);
            }

            // Update password
            $this->userRepository->updateUser($user->id, [
                'password' => $request->new_password
            ]);

            // Log password change
            Log::info('User changed password', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('auth.password_changed_successfully')
            ]);

        } catch (\Exception $e) {
            _log_error('Change password failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('auth.password_change_failed')
            ], 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'emergency_contact' => 'nullable|string|max:255',
                'emergency_phone' => 'nullable|string|max:20',
                'language_preference' => 'nullable|in:es,en',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('auth.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $updateData = $request->only([
                'first_name', 'last_name', 'phone', 'address',
                'emergency_contact', 'emergency_phone', 'language_preference'
            ]);

            $this->userRepository->updateUser($user->id, $updateData);

            // Update session locale if language preference changed
            if (isset($updateData['language_preference'])) {
                session(['locale' => $updateData['language_preference']]);
            }

            return response()->json([
                'success' => true,
                'message' => _trans('auth.profile_updated_successfully'),
                'data' => new UserResource($user->fresh())
            ]);

        } catch (\Exception $e) {
            _log_error('Update profile failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('auth.profile_update_failed')
            ], 500);
        }
    }
}
