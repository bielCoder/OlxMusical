<?php

namespace App\Http\Requests;

use App\Rules\FullName;
use App\Rules\FullNameRule;
use Illuminate\Foundation\Http\FormRequest;

class AuthRegisterRequest extends FormRequest
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
            'image' => ['string'],
            'fullname' => ['required', 'string', new FullNameRule],
            'email' => ['required', 'unique:users,email', 'lowercase', 'string', 'email'],
            'main_phone' => ['required'],
            'optional_phone' => ['nullable'],  // Torna opcional
            'password' => ['required', 'string'],  // 'confirmed' espera 'password_confirmation'
        ];
    }
    
    public function messages()
    {
        return [
            'required' => 'Campo obrigatório.',
            'string' => 'Campo aceita apenas caracteres.',
            'unique' => 'Conta de e-mail já existente.',
            'lowercase' => 'Campo aceita apenas caracteres minúsculos.',
            'email' => 'Campo aceita apenas e-mail.',
            'numeric' => 'Campo aceita apenas números.',
            'confirmed' => 'Senhas não conferem.'
        ];
    }
}