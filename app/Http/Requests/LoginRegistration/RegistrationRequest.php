<?php

namespace App\Http\Requests\LoginRegistration;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
            'name' => 'required|string|max:50|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|min:8|same:password',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Это поле необходимо заполнить',
            'name.string' => 'Логин должен быть строковым',
            'name.max' => 'Максимум 50 символов',
            'name.unique' => 'Данный логин уже занят',
            'email.required' => 'Это поле необходимо заполнить',
            'email.string' => 'Почта должна быть строковой',
            'email.email' => 'Неверный формат почты',
            'email.max' => 'Максимум 255 символов',
            'email.unique' => 'Данная почта уже зарегистрирована',
            'password.required' => 'Это поле необходимо заполнить',
            'password.string' => 'Пароль должен быть строковым',
            'password.min' => 'Минимум 8 символов',
            'password_confirmation.required' => 'Это поле необходимо заполнить',
            'password_confirmation.string' => 'Пароль должен быть строковым',
            'password_confirmation.min' => 'Минимум 8 символов',
            'password_confirmation.same' => 'Пароли не совпали',
        ];
        

    }
}
