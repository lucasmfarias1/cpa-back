<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuizActivateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'deadline' => ['required', 'date', 'after:tomorrow']
        ];
    }

    public function messages()
    {
        return [
            'deadline.required' => 'Selecione uma data limite.',
            'deadline.after' =>
                'A data limite precisa ser no mínimo depois de amanhã.',
        ];
    }
}
