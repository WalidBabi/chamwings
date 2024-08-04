<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegmentationResult extends Model
{
    use HasFactory;

    protected $fillable = ['results'];

    protected $casts = [
        'results' => 'array',
    ];
}