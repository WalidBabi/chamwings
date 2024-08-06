<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleTime extends Model
{
    use HasFactory;

    protected $primaryKey = 'schedule_time_id';
    protected $table = 'schedule_times';
    protected $fillable = [
        'schedule_day_id',
        'departure_time',
        'arrival_time',
        'duration',
    ];
}
