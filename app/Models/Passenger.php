<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    use HasFactory;

    protected $primaryKey = 'passenger_id';
    protected $table = 'passengers';

    protected $fillable = [
<<<<<<< HEAD
        'user_id',
        'travel_requirement_id',
=======
<<<<<<< HEAD
        'user_profile_id',
        'passenger_info_id',
>>>>>>> 4d5a7aa1bf14c0f1d78928c68052db2367164b38
    ];

    public function travelRequirement()
    {
<<<<<<< HEAD
=======
        return $this->belongsTo(PassengerInfo::class, 'passenger_info_id', 'passenger_info_id');
=======
        'user_id',
        'travel_requirement_id',
    ];

    public function travelRequirement()
    {
>>>>>>> 4d5a7aa1bf14c0f1d78928c68052db2367164b38
        return $this->belongsTo(TravelRequirement::class, 'travel_requirement_id', 'travel_requirement_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
<<<<<<< HEAD
=======
>>>>>>> Database-and-Models
>>>>>>> 4d5a7aa1bf14c0f1d78928c68052db2367164b38
    }
}