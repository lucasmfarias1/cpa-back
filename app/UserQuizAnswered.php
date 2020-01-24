<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserQuizAnswered extends Model
{
    protected $table = 'user_quiz_answered';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
