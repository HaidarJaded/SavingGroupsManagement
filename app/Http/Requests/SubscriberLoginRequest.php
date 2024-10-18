<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriberLoginRequest extends FormRequest
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
            'code' => 'required|exists:subscribers,code',
        ];
    }
    public function messages(): array
    {
        return [
            'code.required' => 'يرجى إدخال الكود الخاص بك.',
            'code.exists' => 'الكود الذي أدخلته غير صحيح. يرجى المحاولة مرة أخرى.',
        ];
    }
}
