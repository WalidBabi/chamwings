<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Flight extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'flight_id';
    protected $table = 'flights';
    protected $fillable = [
        'airplane_id',
        'departure_airport',
        'arrival_airport',
        'flight_number',
        'price',
        'departure_terminal',
        'arrival_terminal',
        'duration',
        'miles'
    ];

    public function departureAirport()
    {
        return $this->belongsTo(Airport::class, 'departure_airport', 'airport_id');
    }

    public function arrivalAirport()
    {
        return $this->belongsTo(Airport::class, 'arrival_airport', 'airport_id');
    }

    public function airplane()
    {
        return $this->belongsTo(Airplane::class, 'airplane_id', 'airplane_id');
    }

    public function days()
    {
        return $this->hasMany(ScheduleDay::class, 'flight_id', 'flight_id');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'flight_id', 'flight_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'flight_id', 'flight_id');
    }

    public function scheduleTime()
    {
        return $this->hasMany(ScheduleTime::class, 'flight_id', 'flight_id');
    }
}
