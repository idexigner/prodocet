<?php

namespace App\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserInterface
{
    /**
     * Get all users with pagination
     */
    public function getAllUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get user by ID
     */
    public function getUserById(int $id): ?User;

    /**
     * Get user by email
     */
    public function getUserByEmail(string $email): ?User;

    /**
     * Create new user
     */
    public function createUser(array $data): User;

    /**
     * Update user
     */
    public function updateUser(int $id, array $data): bool;

    /**
     * Delete user (soft delete)
     */
    public function deleteUser(int $id): bool;

    /**
     * Restore soft deleted user
     */
    public function restoreUser(int $id): bool;

    /**
     * Get users by role
     */
    public function getUsersByRole(string $role): Collection;

    /**
     * Assign role to user
     */
    public function assignRoleToUser(int $userId, string $role): bool;

    /**
     * Remove role from user
     */
    public function removeRoleFromUser(int $userId, string $role): bool;

    /**
     * Update user's last login
     */
    public function updateLastLogin(int $userId): bool;

    /**
     * Search users
     */
    public function searchUsers(string $query, array $filters = []): Collection;

    /**
     * Get active users count
     */
    public function getActiveUsersCount(): int;

    /**
     * Get users by status
     */
    public function getUsersByStatus(bool $isActive): Collection;
}
