<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'offer_detail_id';
    protected $table = 'offer_details';
    protected $fillable = [
        'offer_id',
        'days',
        'price',
    ];
}