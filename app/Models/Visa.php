<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Visa extends Model
{
    use HasFactory;

    protected $primaryKey = 'visainfo_id';
    protected $table = 'visainfo';
    protected $fillable = [
        'visa_and_residence',
        'departure_airport',
        'arrival_airport',
    ];

    public function departureVisa()
    {
        return $this->belongsTo(Airport::class, 'departure_airport', 'airport_id');
    }

    public function arrivalVisa()
    {
        return $this->belongsTo(Airport::class, 'arrival_airport', 'airport_id');
    }
}