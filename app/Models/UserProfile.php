<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class UserProfile extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $primaryKey = 'user_profile_id';
    protected $table = 'users_profiles';
    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'email',
        'password',
        'date_of_birth',
        'address',
        'city',
        'mobile',
        'age',
        'gender',
        'nationality',
        'country_of_residence',
    ];

    public function passenger()
    {
        return $this->belongsTo(Passenger::class, 'user_profile_id', 'user_profile_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_profile_id', 'user_profile_id');
    }
}