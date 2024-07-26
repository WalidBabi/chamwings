<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $primaryKey = 'reservation_id';
    protected $table = 'reservations';
    protected $fillable = [
        'passenger_id',
        'flight_id',
        'round_trip',
        'status',
        'is_traveling',
        'have_companions',
        'reservation_date',
    ];

    public function passenger()
    {
        return $this->belongsTo(Passenger::class, 'passenger_id', 'passenger_id');
    }

    public function flights()
    {
        return $this->hasOne(Flight::class, 'flight_id', 'reservation_id');
    }
}