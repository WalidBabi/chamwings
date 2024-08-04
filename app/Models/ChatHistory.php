<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'is_user_message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
