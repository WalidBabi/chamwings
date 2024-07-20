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
        'departure_airport',
        'arrival_airport',
        'airplane_id',
        'flight_number',
        'departure_date',
        'arrival_date',
        'departure_time',
        'arrival_time',
        'duration',
        'number_of_reserved_seats',
        'price',
        'terminal',
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