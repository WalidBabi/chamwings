<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visa extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'visainfo_id';
    protected $table = 'visainfo';
    protected $fillable = [
        'airport_id',
        'visa_and_residence',
        'origin',
        'destination',
    ];

    public function airport()
    {
        return $this->belongsTo(Airport::class, 'airport_id', 'airport_id');
    }
}