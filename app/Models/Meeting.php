<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'meeting_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}