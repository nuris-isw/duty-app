<?php

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LeaveRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Tentukan apakah user bisa meng-update model.
     */
    public function update(User $user, LeaveRequest $leaveRequest): bool
    {
        // Admin boleh melakukan apa saja.
        if ($user->role === 'admin') {
            return true;
        }

        // Atasan boleh meng-update JIKA ID-nya sama dengan atasan_id si pemohon.
        if ($user->role === 'atasan' && $user->id === $leaveRequest->user->atasan_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LeaveRequest $leaveRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LeaveRequest $leaveRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LeaveRequest $leaveRequest): bool
    {
        return false;
    }
}
