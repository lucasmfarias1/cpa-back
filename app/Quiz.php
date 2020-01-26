<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\UserQuizAnswered;
use carbon\Carbon;

class Quiz extends Model
{
    protected $fillable = ['name', 'deadline', 'status'];
    protected $appends = ['question_count', 'status_text', 'is_available'];

    const STATUS_LIST = ['pendente', 'ativo', 'encerrado'];

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

    public function getIsAvailableAttribute()
    {
        if (is_null($this->deadline)) return false;

        $deadline = Carbon::createFromFormat('Y-m-d', $this->deadline)->endOfDay();
        if ($this->status != 1 || $deadline < Carbon::now()) {
            return false;
        } else {
            return true;
        }
    }
}
