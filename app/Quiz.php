<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['name', 'deadline', 'status'];
    protected $appends = ['question_count', 'status_text'];

    const STATUS_LIST = ['pendente', 'ativo', 'finalizado'];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function getQuestionCountAttribute()
    {
        return $this->questions->count();
    }

    public function getStatusTextAttribute()
    {
        return self::STATUS_LIST[$this->status];
    }
}
