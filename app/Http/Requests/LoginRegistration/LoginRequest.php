<?php

namespace App\Http\Requests\LoginRegistration;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Это поле необходимо заполнить',
            'email.string' => 'Почта должна быть строковой',
            'email.email' => 'Неверный формат почты',
            'email.exists' => 'Пользователь не найден',
            'password.required' => 'Это поле необходимо заполнить',
            'password.string' => 'Пароль должен быть строковым',
        ];
    }
}
