<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use carbon\Carbon;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'birthdate',
        'sex',
        'term',
        'course_id',
        'id_legacy'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['age', 'has_completed_profile'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // RELATIONSHIPS
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // ACCESSORS
    public function getAgeAttribute()
    {
        return Carbon::parse($this->birthdate)->age;
    }

    public function getHasCompletedProfileAttribute()
    {
        return !(
            is_null($this->term) ||
            is_null($this->birthdate) ||
            is_null($this->course_id) ||
            is_null($this->sex)
        );
    }

    // UNTESTED
    public function answeredQuiz($quizId)
    {
        return UserQuizAnswered::where([
            'user_id' => $this->id,
            'quiz_id' => $quizId
        ])->exists();
    }
}
