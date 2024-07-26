<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $primaryKey = 'city_id';
    protected $table = 'cities';
    protected $fillable = [
        'code',
        'city_name',
    ];
}