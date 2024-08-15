<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'offer_detail_id';
    protected $table = 'offer_details';
    protected $fillable = [
        'offer_id',
        'days',
        'price',
    ];
}
