<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $primaryKey = 'user_id';
    protected $table = 'users';
    protected $fillable = [
        'email',
        'password',
        'phone',
    ];

    public function passenger()
    {
        return $this->hasOne(Passenger::class, 'user_id', 'user_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id', 'user_id');
    }
}