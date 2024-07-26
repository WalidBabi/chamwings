<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;

    protected $primaryKey = 'flight_id';
    protected $table = 'flights';
    protected $fillable = [
        'airplane_id',
        'departure_airport',
        'arrival_airport',
        'flight_number',
        'number_of_reserved_seats',
        'price',
        'terminal',
=======
        'airplane_id',
        'departure_airport',
        'arrival_airport',
        'flight_number',
        'number_of_reserved_seats',
        'price',
        'departure_terminal',
        'arrival_terminal',
>>>>>>> Database-and-Models
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
}