<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'thread_id', // Add thread_id to fillable fields
        'input_text',
        'response_text',
        'chat_history'
    ];

    protected $casts = [
        'chat_history' => 'array', // Cast chat_history to array
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
