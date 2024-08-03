<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    public function chatHistories()
    {
        return $this->hasMany(ChatHistory::class, 'user_id');
    }
}
