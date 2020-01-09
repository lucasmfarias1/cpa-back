<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['name', 'deadline', 'active'];
    protected $appends = ['question_count'];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function getQuestionCountAttribute()
    {
        return $this->questions->count();
    }
}
