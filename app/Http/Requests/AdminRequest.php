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
        $id = $this->route('admin') ? $this->route('admin')->id : 0;
        return [
            'name' => ['required', 'string', 'min:4', 'max:100'],
            'cpf' => ['required', 'string', 'size:11', "unique:users,cpf,$id"],
            'course_id' => ['required', 'exists:courses,id'],
            'password' => [
                'sometimes',
                'required',
                'confirmed',
                'string',
                'min:4',
                'max:100'
            ]
        ];
    }

    public function messages()
    {
        return [
            'name.min' => 'O nome precisa ter no mínimo 4 caracteres.',
            'cpf.unique' => 'Este CPF já existe na base de dados.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'password.min' => 'A senha precisa ter no mínimo 4 caracteres.'
        ];
    }
}
