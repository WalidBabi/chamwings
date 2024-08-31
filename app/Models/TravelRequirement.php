<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelRequirement extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'travel_requirement_id';
    protected $table = 'travel_requirements';
    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'date_of_birth',
        'address',
        'city',
        'id_number',
        'mobile_during_travel',
        'age',
        'gender',
        'nationality',
        'country_of_residence',
    ];

    public function passports()
    {
        return $this->hasMany(Passport::class, 'travel_requirement_id', 'travel_requirement_id');
    }

    public function passenger()
    {
        return $this->hasOne(Passenger::class, 'travel_requirement_id', 'travel_requirement_id');
    }

    public function companion()
    {
        return $this->hasOne(Companion::class, 'travel_requirement_id', 'travel_requirement_id');
    }
}