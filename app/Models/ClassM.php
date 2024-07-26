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
        'class_name',
        'price_rate',
        'weight_allowed',
        'number_of_meals',
        'number_of_seats',
    ];
}