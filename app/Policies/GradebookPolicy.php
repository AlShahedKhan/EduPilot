<?php
namespace App\Policies;
use App\Models\User;
class GradebookPolicy {
 public function viewAny(User $user): bool { return $this->isActive($user) && ($this->isPrimary($user) || $this->hasPermission($user,'admin.gradebook')); }
 public function create(User $user): bool { return $this->isPrimary($user); }
 public function update(User $user): bool { return $this->isPrimary($user); }
 private function isActive(User $user): bool { return (int)$user->role_id===2 && $user->account_status!=='disable'; }
 private function isPrimary(User $user): bool { return $this->isActive($user) && (int)$user->school_role===1; }
 private function hasPermission(User $user,string $permission): bool { $permissions=json_decode($user->menu_permission,true); return is_array($permissions)&&in_array($permission,$permissions,true); }
}
