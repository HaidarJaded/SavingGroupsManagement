<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
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
            'saving_group_id' => 'required|exists:saving_groups,id',
            'subscriber_id' => 'required|exists:subscribers,id',
            'day_number' => 'required|integer|min:1',
            'cycle_number' => 'required|integer|min:1',
        ];
    }
}
