<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Passport extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'passport_id';
    protected $table = 'passports';
    protected $fillable = [
        'travel_requirement_id',
        'number',
        'status',
        'passport_expiry_date',
        'passport_issued_date',
        'passport_issued_country',
        'passport_image',
    ];

    protected $decryptable = ['number'];

    public function travelRequirement()
    {
        return $this->belongsTo(TravelRequirement::class, 'travel_requirement_id', 'travel_requirement_id');
    }
}