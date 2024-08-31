<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRole extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'role_user_id';
    protected $table = 'roles_users';
    protected $fillable = [
        'employee_id',
        'role_id',
    ];
}