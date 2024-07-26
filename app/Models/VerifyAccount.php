<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class VerifyAccount extends Authenticatable
{
    use HasFactory, HasApiTokens;
    
    protected $table = 'password_resets';
    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'email',
        'password',
        'date_of_birth',
        'address',
        'city',
<<<<<<< HEAD
        'phone',
=======
<<<<<<< HEAD
        'mobile',
=======
        'phone',
>>>>>>> Database-and-Models
>>>>>>> 4d5a7aa1bf14c0f1d78928c68052db2367164b38
        'age',
        'gender',
        'nationality',
        'country_of_residence',
        'code',
        'created_at'
    ];

    public $timestamps = false;
}