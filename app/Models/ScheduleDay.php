<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleDay extends Model
{
    use HasFactory, SoftDeletes;

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

    public function flight(){
        return $this->belongsTo(Flight::class, 'flight_id', 'flight_id');
    }
}