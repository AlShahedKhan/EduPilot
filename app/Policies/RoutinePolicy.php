<?php
namespace App\Policies;
use App\Models\Routine;
use App\Models\User;
class RoutinePolicy {
 public function viewAny(User $user): bool { return $this->isActive($user) && ($this->isPrimary($user) || $this->hasPermission($user,'admin.routine')); }
 public function view(User $user,Routine $routine): bool { return $this->viewAny($user) && (int)$user->school_id===(int)$routine->school_id; }
 public function create(User $user): bool { return $this->isPrimary($user); }
 public function update(User $user,Routine $routine): bool { return $this->isPrimary($user) && (int)$user->school_id===(int)$routine->school_id; }
 public function delete(User $user,Routine $routine): bool { return $this->update($user,$routine); }
 private function isActive(User $user): bool { return (int)$user->role_id===2 && $user->account_status!=='disable'; }
 private function isPrimary(User $user): bool { return $this->isActive($user) && (int)$user->school_role===1; }
 private function hasPermission(User $user,string $permission): bool { $permissions=json_decode($user->menu_permission,true); return is_array($permissions)&&in_array($permission,$permissions,true); }
}
