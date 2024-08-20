<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassM extends Model
{
    use HasFactory;

    protected $primaryKey = 'class_id';
    protected $table = 'classes';
    protected $fillable = [
        'airplane_id',
        'class_name',
        'price_rate',
        'weight_allowed',
        'cabin_weight',
        'number_of_meals',
        'number_of_seats',
    ];

    public function airplane()
    {
        return $this->belongsTo(Airplane::class, 'airplane_id', 'airplane_id');
    }

    public function seats()
    {
        return $this->hasMany(Seat::class, 'class_id', 'class_id');
    }
}
