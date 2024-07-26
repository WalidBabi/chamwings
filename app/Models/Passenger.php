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
        'user_profile_id',
        'passenger_info_id',
    ];

    public function passengerInfo()
    {
        return $this->belongsTo(PassengerInfo::class, 'passenger_info_id', 'passenger_info_id');
=======
        'user_id',
        'travel_requirement_id',
    ];

    public function travelRequirement()
    {
        return $this->belongsTo(TravelRequirement::class, 'travel_requirement_id', 'travel_requirement_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
>>>>>>> Database-and-Models
    }
}