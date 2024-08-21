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
    }
    public function companions()
    {
        return $this->hasMany(Companion::class, 'passenger_id', 'passenger_id');
    }
    
    

}