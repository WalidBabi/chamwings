<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleTime extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'schedule_time_id';
    protected $table = 'schedule_times';
    protected $fillable = [
        'schedule_day_id',
        'departure_time',
        'arrival_time',
        'duration',
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class, 'flight_id', 'flight_id');
    }

    public function day()
    {
        return $this->belongsTo(ScheduleDay::class, 'schedule_day_id', 'schedule_day_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'schedule_time_id', 'schedule_time_id');
    }
}
