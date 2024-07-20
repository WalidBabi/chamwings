<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $primaryKey = 'employee_id';
    protected $table = 'employees';
    protected $fillable = [
        'user_profile_id',
        'job_title',
        'department',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_users', 'employee_id', 'role_id');
    }
}