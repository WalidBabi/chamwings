<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Companion extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'companion_id';
    protected $table = 'companions';
    protected $fillable = [
        'passenger_id',
        'travel_requirement_id',
        'infant',
    ];

    public function travelRequirement()
    {
        return $this->belongsTo(TravelRequirement::class, 'travel_requirement_id', 'travel_requirement_id');
    }

    public function passenger()
    {
        return $this->belongsTo(Passenger::class);
    }

    public function passports(): HasManyThrough
    {
        return $this->hasManyThrough(Passport::class, TravelRequirement::class,'travel_requirement_id','travel_requirement_id');
    }
}