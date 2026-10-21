<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        if ((int) $user->role_id !== 2 || $user->account_status === 'disable') {
            return false;
        }

        if ((int) $user->school_role === 1) {
            return true;
        }

        $permissions = json_decode($user->menu_permission, true);

        return is_array($permissions) && in_array('admin.admin', $permissions, true);
    }

    public function view(User $user, User $model): bool
    {
        return $this->viewAny($user) && $this->isAdminFromSameSchool($user, $model);
    }

    public function create(User $user): bool
    {
        return $this->isPrimaryAdmin($user);
    }

    public function update(User $user, User $model): bool
    {
        return $this->isPrimaryAdmin($user) && $this->isAdminFromSameSchool($user, $model);
    }

    public function delete(User $user, User $model): bool
    {
        return $this->canManageSecondaryAdmin($user, $model);
    }

    public function updateStatus(User $user, User $model): bool
    {
        return $this->canManageSecondaryAdmin($user, $model);
    }

    public function updatePassword(User $user, User $model): bool
    {
        return $this->isPrimaryAdmin($user) && $this->isAdminFromSameSchool($user, $model);
    }

    public function managePermissions(User $user, User $model): bool
    {
        return $this->canManageSecondaryAdmin($user, $model);
    }

    public function viewAnyTeacher(User $user): bool
    {
        return $this->isActiveSchoolAdmin($user)
            && ($this->isPrimaryAdmin($user) || $this->hasPermission($user, 'admin.teacher'));
    }

    public function viewTeacher(User $user, User $teacher): bool
    {
        return $this->viewAnyTeacher($user) && $this->isTeacherFromSameSchool($user, $teacher);
    }

    public function createTeacher(User $user): bool
    {
        return $this->isPrimaryAdmin($user);
    }

    public function updateTeacher(User $user, User $teacher): bool
    {
        return $this->isPrimaryAdmin($user) && $this->isTeacherFromSameSchool($user, $teacher);
    }

    public function deleteTeacher(User $user, User $teacher): bool
    {
        return $this->updateTeacher($user, $teacher);
    }

    public function updateTeacherStatus(User $user, User $teacher): bool
    {
        return $this->updateTeacher($user, $teacher);
    }

    public function updateTeacherPassword(User $user, User $teacher): bool
    {
        return $this->updateTeacher($user, $teacher);
    }

    public function manageTeacherDocuments(User $user, User $teacher): bool
    {
        return $this->updateTeacher($user, $teacher);
    }

    public function viewAnyAccountant(User $user): bool
    {
        return $this->canViewDirectory($user, 'admin.accountant');
    }

    public function viewAccountant(User $user, User $accountant): bool
    {
        return $this->viewAnyAccountant($user) && $this->isRoleFromSameSchool($user, $accountant, 4);
    }

    public function createAccountant(User $user): bool
    {
        return $this->isPrimaryAdmin($user);
    }

    public function updateAccountant(User $user, User $accountant): bool
    {
        return $this->isPrimaryAdmin($user) && $this->isRoleFromSameSchool($user, $accountant, 4);
    }

    public function deleteAccountant(User $user, User $accountant): bool
    {
        return $this->updateAccountant($user, $accountant);
    }

    public function updateAccountantStatus(User $user, User $accountant): bool
    {
        return $this->updateAccountant($user, $accountant);
    }

    public function updateAccountantPassword(User $user, User $accountant): bool
    {
        return $this->updateAccountant($user, $accountant);
    }

    public function manageAccountantDocuments(User $user, User $accountant): bool
    {
        return $this->updateAccountant($user, $accountant);
    }

    public function viewAnyLibrarian(User $user): bool
    {
        return $this->canViewDirectory($user, 'admin.librarian');
    }

    public function viewLibrarian(User $user, User $librarian): bool
    {
        return $this->viewAnyLibrarian($user) && $this->isRoleFromSameSchool($user, $librarian, 5);
    }

    public function createLibrarian(User $user): bool
    {
        return $this->isPrimaryAdmin($user);
    }

    public function updateLibrarian(User $user, User $librarian): bool
    {
        return $this->isPrimaryAdmin($user) && $this->isRoleFromSameSchool($user, $librarian, 5);
    }

    public function deleteLibrarian(User $user, User $librarian): bool
    {
        return $this->updateLibrarian($user, $librarian);
    }

    public function updateLibrarianStatus(User $user, User $librarian): bool
    {
        return $this->updateLibrarian($user, $librarian);
    }

    public function updateLibrarianPassword(User $user, User $librarian): bool
    {
        return $this->updateLibrarian($user, $librarian);
    }

    public function manageLibrarianDocuments(User $user, User $librarian): bool
    {
        return $this->updateLibrarian($user, $librarian);
    }

    public function viewAnyParent(User $user): bool
    {
        return $this->canViewDirectory($user, 'admin.parent');
    }

    public function viewParent(User $user, User $parent): bool
    {
        return $this->viewAnyParent($user) && $this->isRoleFromSameSchool($user, $parent, 6);
    }

    public function createParent(User $user): bool
    {
        return $this->isPrimaryAdmin($user);
    }

    public function updateParent(User $user, User $parent): bool
    {
        return $this->isPrimaryAdmin($user) && $this->isRoleFromSameSchool($user, $parent, 6);
    }

    public function deleteParent(User $user, User $parent): bool
    {
        return $this->updateParent($user, $parent);
    }

    public function updateParentStatus(User $user, User $parent): bool
    {
        return $this->updateParent($user, $parent);
    }

    public function updateParentPassword(User $user, User $parent): bool
    {
        return $this->updateParent($user, $parent);
    }

    public function manageParentDocuments(User $user, User $parent): bool
    {
        return $this->updateParent($user, $parent);
    }

    public function viewAnyWarden(User $user): bool
    {
        return $this->canViewDirectory($user, 'admin.warden');
    }

    public function viewWarden(User $user, User $warden): bool
    {
        return $this->viewAnyWarden($user) && $this->isRoleFromSameSchool($user, $warden, 10);
    }

    public function createWarden(User $user): bool
    {
        return $this->isPrimaryAdmin($user);
    }

    public function updateWarden(User $user, User $warden): bool
    {
        return $this->isPrimaryAdmin($user) && $this->isRoleFromSameSchool($user, $warden, 10);
    }

    public function deleteWarden(User $user, User $warden): bool
    {
        return $this->updateWarden($user, $warden);
    }

    public function updateWardenStatus(User $user, User $warden): bool
    {
        return $this->updateWarden($user, $warden);
    }

    public function updateWardenPassword(User $user, User $warden): bool
    {
        return $this->updateWarden($user, $warden);
    }

    public function manageWardenDocuments(User $user, User $warden): bool
    {
        return $this->updateWarden($user, $warden);
    }

    public function viewTeacherPermissions(User $user): bool
    {
        return $this->canViewDirectory($user, 'admin.teacher.permission');
    }

    public function manageTeacherPermissions(User $user): bool
    {
        return $this->isPrimaryAdmin($user);
    }

    private function canManageSecondaryAdmin(User $user, User $model): bool
    {
        return $this->isPrimaryAdmin($user)
            && $this->isAdminFromSameSchool($user, $model)
            && $user->isNot($model)
            && (int) $model->school_role !== 1;
    }

    private function isPrimaryAdmin(User $user): bool
    {
        return (int) $user->role_id === 2
            && (int) $user->school_role === 1
            && $user->account_status !== 'disable';
    }

    private function isActiveSchoolAdmin(User $user): bool
    {
        return (int) $user->role_id === 2 && $user->account_status !== 'disable';
    }

    private function hasPermission(User $user, string $permission): bool
    {
        $permissions = json_decode($user->menu_permission, true);

        return is_array($permissions) && in_array($permission, $permissions, true);
    }

    private function canViewDirectory(User $user, string $permission): bool
    {
        return $this->isActiveSchoolAdmin($user)
            && ($this->isPrimaryAdmin($user) || $this->hasPermission($user, $permission));
    }

    private function isRoleFromSameSchool(User $user, User $target, int $roleId): bool
    {
        return (int) $target->role_id === $roleId
            && (int) $target->school_id === (int) $user->school_id;
    }

    private function isAdminFromSameSchool(User $user, User $model): bool
    {
        return (int) $model->role_id === 2
            && (int) $model->school_id === (int) $user->school_id;
    }

    private function isTeacherFromSameSchool(User $user, User $teacher): bool
    {
        return (int) $teacher->role_id === 3
            && (int) $teacher->school_id === (int) $user->school_id;
    }
}
