<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_id';
    protected $table = 'employees';
    protected $fillable = [
        'user_id',
        'name',
        'department',
        'job_title',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_users', 'employee_id', 'role_id');
    }

    public function rolesEmployee()
    {
        return $this->hasMany(UserRole::class, 'employee_id', 'employee_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}