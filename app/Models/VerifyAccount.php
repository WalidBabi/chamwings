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
        'phone',
        'age',
        'gender',
        'nationality',
        'country_of_residence',
        'code',
        'created_at'
    ];

    public $timestamps = false;
}