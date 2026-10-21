<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;

class SubjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isActiveSchoolAdmin($user)
            && ($this->isPrimaryAdmin($user) || $this->hasPermission($user, 'admin.subject_list'));
    }

    public function view(User $user, Subject $subject): bool
    {
        return $this->viewAny($user) && (int) $subject->school_id === (int) $user->school_id;
    }

    public function create(User $user): bool
    {
        return $this->isPrimaryAdmin($user);
    }

    public function update(User $user, Subject $subject): bool
    {
        return $this->isPrimaryAdmin($user) && (int) $subject->school_id === (int) $user->school_id;
    }

    public function delete(User $user, Subject $subject): bool
    {
        return $this->update($user, $subject);
    }

    private function isActiveSchoolAdmin(User $user): bool
    {
        return (int) $user->role_id === 2 && $user->account_status !== 'disable';
    }

    private function isPrimaryAdmin(User $user): bool
    {
        return $this->isActiveSchoolAdmin($user) && (int) $user->school_role === 1;
    }

    private function hasPermission(User $user, string $permission): bool
    {
        $permissions = json_decode($user->menu_permission, true);

        return is_array($permissions) && in_array($permission, $permissions, true);
    }
}
