<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnswerCard extends Model
{
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
