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
        $id = $this->route('admin') ? $this->route('admin') : 0;
        return [
            'name' => ['required', 'string', 'min:4', 'max:100'],
            'cpf' => ['required', 'string', 'size:11', "unique:users,cpf,$id"],
            'course_id' => ['required', 'exists:courses,id'],
            'password' => [
                'required',
                'confirmed',
                'string',
                'min:4',
                'max:100'
            ]
        ];
    }
}
