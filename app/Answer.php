<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $guarded = [];

    public function answerCard()
    {
        return $this->belongsTo(AnswerCard::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
