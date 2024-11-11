<?php

namespace App\Http\Requests\Friend;

use Illuminate\Foundation\Http\FormRequest;

class FindUserRequest extends FormRequest
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
            'name' => 'required|string|max:50|exists:users,name'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Это поле необходимо заполнить',
            'name.string' => 'Логин должен быть строковым',
            'name.max' => 'Максимум 50 символов',
            'name.exists' => 'Пользователь не найден',
        ];
    }
}
