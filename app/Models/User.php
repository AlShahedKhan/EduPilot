<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'parent_id',
        'school_id',
        'code',
        'user_information',
        'student_info',
        'documents',
        'status',
        'department_id',
        'designation',
        'language',
        'school_role',
        'account_status',
        'menu_permission',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function checkEnrollment()
    {
        return $this->hasMany(Enrollment::class, 'user_id');
    }
    public function getTomoNameAttribute()
    {
        return $this->checkEnrollment()->class_id;
    }

    public function joinedClubs()
    {
        return $this->hasMany(ClubMember::class, 'student_id');
    }
    public function enrollment()
    {
        return $this->hasOne(Enrollment::class, 'user_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function hostels(): HasMany
    {
        return $this->hasMany(Hostel::class, 'warden_id');
    }

    public function teacherPermissions(): HasMany
    {
        return $this->hasMany(TeacherPermission::class, 'teacher_id');
    }

    public function dailyAttendances(): HasMany
    {
        return $this->hasMany(DailyAttendances::class, 'student_id');
    }

    public function gradebooks(): HasMany
    {
        return $this->hasMany(Gradebook::class, 'student_id');
    }
}
