<?php

namespace App\Repositories;

use App\Models\User;
use App\Interfaces\UserInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserRepository implements UserInterface
{
    /**
     * Get all users with pagination
     */
    public function getAllUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        try {
            $query = User::with('roles');

            // Apply filters
            if (isset($filters['search']) && !empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('document_id', 'like', "%{$search}%");
                });
            }

            if (isset($filters['role']) && !empty($filters['role'])) {
                $query->whereHas('roles', function ($q) use ($filters) {
                    $q->where('name', $filters['role']);
                });
            }

            if (isset($filters['status'])) {
                $query->where('is_active', $filters['status']);
            }

            if (isset($filters['language']) && !empty($filters['language'])) {
                $query->where('language_preference', $filters['language']);
            }

            return $query->orderBy('created_at', 'desc')->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('UserRepository: getAllUsers failed', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get user by ID
     */
    public function getUserById(int $id): ?User
    {
        try {
            return User::with('roles', 'permissions')->find($id);
        } catch (\Exception $e) {
            Log::error('UserRepository: getUserById failed', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get user by email
     */
    public function getUserByEmail(string $email): ?User
    {
        try {
            return User::where('email', $email)->first();
        } catch (\Exception $e) {
            Log::error('UserRepository: getUserByEmail failed', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create new user
     */
    public function createUser(array $data): User
    {
        try {
            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // Set default values
            $data['is_active'] = $data['is_active'] ?? true;
            $data['language_preference'] = $data['language_preference'] ?? 'es';

            $user = User::create($data);

            // Assign role if provided
            if (isset($data['role'])) {
                $user->assignRole($data['role']);
            }

            return $user->load('roles');
        } catch (\Exception $e) {
            Log::error('UserRepository: createUser failed', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update user
     */
    public function updateUser(int $id, array $data): bool
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return false;
            }

            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);

            // Update role if provided
            if (isset($data['role'])) {
                $user->syncRoles([$data['role']]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('UserRepository: updateUser failed', [
                'id' => $id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete user (soft delete)
     */
    public function deleteUser(int $id): bool
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return false;
            }

            return $user->delete();
        } catch (\Exception $e) {
            Log::error('UserRepository: deleteUser failed', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Restore soft deleted user
     */
    public function restoreUser(int $id): bool
    {
        try {
            $user = User::withTrashed()->find($id);
            if (!$user) {
                return false;
            }

            return $user->restore();
        } catch (\Exception $e) {
            Log::error('UserRepository: restoreUser failed', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get users by role
     */
    public function getUsersByRole(string $role): Collection
    {
        try {
            return User::role($role)->with('roles')->get();
        } catch (\Exception $e) {
            Log::error('UserRepository: getUsersByRole failed', [
                'role' => $role,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Assign role to user
     */
    public function assignRoleToUser(int $userId, string $role): bool
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return false;
            }

            $user->assignRole($role);
            return true;
        } catch (\Exception $e) {
            Log::error('UserRepository: assignRoleToUser failed', [
                'user_id' => $userId,
                'role' => $role,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Remove role from user
     */
    public function removeRoleFromUser(int $userId, string $role): bool
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return false;
            }

            $user->removeRole($role);
            return true;
        } catch (\Exception $e) {
            Log::error('UserRepository: removeRoleFromUser failed', [
                'user_id' => $userId,
                'role' => $role,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update user's last login
     */
    public function updateLastLogin(int $userId): bool
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return false;
            }

            $user->update(['last_login_at' => now()]);
            return true;
        } catch (\Exception $e) {
            Log::error('UserRepository: updateLastLogin failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Search users
     */
    public function searchUsers(string $query, array $filters = []): Collection
    {
        try {
            $searchQuery = User::with('roles')
                ->where(function ($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                      ->orWhere('last_name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%")
                      ->orWhere('document_id', 'like', "%{$query}%");
                });

            // Apply additional filters
            if (isset($filters['role'])) {
                $searchQuery->whereHas('roles', function ($q) use ($filters) {
                    $q->where('name', $filters['role']);
                });
            }

            if (isset($filters['status'])) {
                $searchQuery->where('is_active', $filters['status']);
            }

            return $searchQuery->limit(50)->get();
        } catch (\Exception $e) {
            Log::error('UserRepository: searchUsers failed', [
                'query' => $query,
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get active users count
     */
    public function getActiveUsersCount(): int
    {
        try {
            return User::where('is_active', true)->count();
        } catch (\Exception $e) {
            Log::error('UserRepository: getActiveUsersCount failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get users by status
     */
    public function getUsersByStatus(bool $isActive): Collection
    {
        try {
            return User::where('is_active', $isActive)->with('roles')->get();
        } catch (\Exception $e) {
            Log::error('UserRepository: getUsersByStatus failed', [
                'is_active' => $isActive,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
