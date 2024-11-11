<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'chatId' => 'required|exists:chats,id',
            'message' => 'required|string|max:300',
            'receiverId' => 'required|exists:users,id'
        ];
    }

    public function messages()
    {
        return [
            'chatId.required' => 'Чат не найден',
            'chatId.exists' => 'Чат не найден',
            'message.required' => 'Сообщение не может быть пустым',
            'message.string' => 'Сообщение должно быть строкой',
            'message.max' => 'Сообщение не может быть больше 300 символов',
            'receiverId.required' => 'Получатель не может быть пустым',
            'receiverId.exists' => 'Получатель не найден',
        ];
    }
}
