<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seat extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'seat_id';
    protected $table = 'seats';
    protected $fillable = [
        'class_id',
        'seat_number',
        'row_number',
        'checked',
    ];

    public function class()
    {
        return $this->belongsTo(ClassM::class, 'class_id', 'class_id');
    }

    public function flightSeat()
    {
        return $this->hasMany(FlightSeat::class, 'seat_id', 'seat_id');
    }
}