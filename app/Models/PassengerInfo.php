<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassengerInfo extends Model
{
    use HasFactory;

    protected $primaryKey = 'passenger_info_id';
    protected $table = 'passengers_info';
    protected $fillable = [
        'passport',
        'passport_issued_country',
        'passport_expiry_date',
        'mobile_during_travel',
        'passport_image',
        'id_number',
    ];
}
