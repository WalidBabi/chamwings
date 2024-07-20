<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    use HasFactory;

    protected $primaryKey = 'airport_id';
    protected $table = 'airports';
    protected $fillable = [
        'airport_name',
        'city',
        'country',
    ];
}
