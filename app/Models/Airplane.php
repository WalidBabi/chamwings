<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airplane extends Model
{
    use HasFactory;

    protected $primaryKey = 'airplane_id';
    protected $table = 'airplanes';
    protected $fillable = [
        'model',
        'capacity',
        'business_seats_number',
        'economy_seats_number',
        'manufacturer',
        'range',
    ];
}