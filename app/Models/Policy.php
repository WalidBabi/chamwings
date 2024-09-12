<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;

    protected $primaryKey = 'policy_id';
    protected $table = 'policies';
    protected $fillable = [
        'policy_name',
        'value',
    ];
}