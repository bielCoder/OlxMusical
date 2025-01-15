<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthLoginRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => ['string','email'],
            'password' => ['required','string']
        ];
    }


    public function messages()
    {
        return [
            'required' => '* campo obrigatório.',
            'string' => '* campo aceita apenas caracteres.',
            'email' => '* campo aceita apenas e-mail.'
        ];
    }
}