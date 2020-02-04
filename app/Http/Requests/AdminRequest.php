<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'min:4', 'max:100'],
            'cpf' => ['required', 'string', 'size:11', 'unique:users,cpf'],
            'course_id' => ['required', 'exists:courses,id'],
            'password' => ['required', 'string', 'min:4', 'max:100']
        ];
    }
}
