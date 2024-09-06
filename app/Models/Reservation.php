<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'reservation_id';
    protected $table = 'reservations';
    protected $fillable = [
        'passenger_id',
        'flight_id',
        'round_flight_id',
        'schedule_time_id',
        'round_schedule_time_id',
        'round_trip',
        'status',
        'is_traveling',
        'have_companions',
        'reservation_date',
        'infants'
    ];

    public function passenger()
    {
        return $this->belongsTo(Passenger::class, 'passenger_id', 'passenger_id');
    }

    public function flight()
    {
        return $this->hasOne(Flight::class, 'flight_id', 'flight_id');
    }

    public function roundFlight()
    {
        return $this->hasOne(Flight::class, 'flight_id', 'round_flight_id');
    }

    public function seats()
    {
        return $this->belongsToMany(Seat::class, 'flight_seats', 'reservation_id', 'seat_id');
    }

    public function flightSeats()
    {
        return $this->hasMany(FlightSeat::class, 'reservation_id', 'reservation_id');
    }

    public function time()
    {
        return $this->belongsTo(ScheduleTime::class, 'schedule_time_id', 'schedule_time_id');
    }

    public function roundTime()
    {
        return $this->belongsTo(ScheduleTime::class, 'round_schedule_time_id', 'schedule_time_id');
    }
}