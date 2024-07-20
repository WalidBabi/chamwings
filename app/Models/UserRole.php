<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    protected $primaryKey = 'role_user_id';
    protected $table = 'roles_users';
    protected $fillable = [
        'employee_id',
        'role_id',
    ];
}