<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\UserQuizAnswered;
use carbon\Carbon;

class Quiz extends Model
{
    protected $fillable = ['name', 'deadline', 'status', 'course_id'];
    protected $appends = [
        'question_count',
        'status_text',
        'is_available',
        'course_name'
    ];

    const STATUS_LIST = ['pendente', 'ativo', 'encerrado', 'arquivado'];

    public static function boot() {
        parent::boot();

        static::deleting(function($quiz) {
             $quiz->questions()->delete();
             $quiz->answerCards()->delete();
        });
    }

    //
    // RELATIONSHIPS
    //
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function answerCards()
    {
        return $this->hasMany(AnswerCard::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    //
    // ACCESSORS
    //
    public function getCourseNameAttribute()
    {
        if ($this->course) return $this->course->shorthand;
        else return "TODOS";
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
