<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

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
}
