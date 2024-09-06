<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlightSeat extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'flight_seat_id';
    protected $table = 'flight_seats';
    protected $fillable = [
        'seat_id',
        'is_round_flight',
        'reservation_id',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id', 'reservation_id');
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class, 'seat_id', 'seat_id');
    }
}
