<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleDay extends Model
{
    use HasFactory;

    protected $primaryKey = 'schedule_day_id';
    protected $table = 'schedule_days';
    protected $fillable = [
        'flight_id',
        'departure_date',
        'arrival_date',
    ];

    public function times()
    {
        return $this->hasMany(ScheduleTime::class, 'schedule_day_id', 'schedule_day_id');
    }
}
