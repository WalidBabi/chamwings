<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Offer extends Model
{
    use HasFactory;

    protected $primaryKey = 'offer_id';
    protected $table = 'offers';
    protected $fillable = [
        'employee_id',
        'flight_id',
        'description',
        'start_date',
        'end_date',
        'image',
        'title',
        'discount',
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class, 'flight_id', 'flight_id');
    }
}