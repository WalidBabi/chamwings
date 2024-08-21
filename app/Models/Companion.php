<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Companion extends Model
{
    use HasFactory;
    protected $fillable = [
        'companion_id',
        'passenger_id',
        'travel_requirement_id',
        'infant'
    ];

    public function passenger()
    {
        return $this->belongsTo(Passenger::class);
    }

    public function travelRequirement()
    {
        return $this->belongsTo(TravelRequirement::class, 'travel_requirement_id', 'travel_requirement_id');
    }

}
