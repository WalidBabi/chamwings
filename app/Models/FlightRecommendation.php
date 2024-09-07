<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recommended_flights',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
