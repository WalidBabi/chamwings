<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'passenger_id',
        'recommended_flights',
    ];

    public function passenger(){
        return $this->belongsTo(User::class, 'passenger_id', 'passenger_id');
    }
}
