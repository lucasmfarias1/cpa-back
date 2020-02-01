<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnswerCard extends Model
{
    public static function boot() {
        parent::boot();

        static::deleting(function($answerCard) {
             $answerCard->answers()->delete();
        });
    }

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
