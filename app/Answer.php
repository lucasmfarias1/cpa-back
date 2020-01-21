<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    public function answerCard()
    {
        return $this->belongsTo(AnswerCard::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
