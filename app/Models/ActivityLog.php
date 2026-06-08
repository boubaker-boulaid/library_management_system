<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'book_id', 'action', 'book_title', 'details'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
