<?php
namespace App\Policies;
use App\Models\Syllabus;
use App\Models\User;
class SyllabusPolicy {
 public function viewAny(User $user): bool { return $this->isActive($user) && ($this->isPrimary($user) || $this->hasPermission($user,'admin.syllabus')); }
 public function view(User $user,Syllabus $syllabus): bool { return $this->viewAny($user) && (int)$user->school_id===(int)$syllabus->school_id; }
 public function create(User $user): bool { return $this->isPrimary($user); }
 public function update(User $user,Syllabus $syllabus): bool { return $this->isPrimary($user) && (int)$user->school_id===(int)$syllabus->school_id; }
 public function delete(User $user,Syllabus $syllabus): bool { return $this->update($user,$syllabus); }
 private function isActive(User $user): bool { return (int)$user->role_id===2 && $user->account_status!=='disable'; }
 private function isPrimary(User $user): bool { return $this->isActive($user) && (int)$user->school_role===1; }
 private function hasPermission(User $user,string $permission): bool { $permissions=json_decode($user->menu_permission,true); return is_array($permissions)&&in_array($permission,$permissions,true); }
}
