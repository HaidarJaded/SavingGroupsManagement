<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSavingGroupRequest extends FormRequest
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
            'name' => 'required',
            'subscribers_count' => 'required|integer',
            'amount_per_day' => 'required|numeric',
            'days_per_cycle' => 'required|integer',
            'start_date' => 'required|date',
            'subscribers.*.name' => 'required|string',
            'subscribers.*.last_name' => 'required|string',
            'subscribers.*.phone' => 'nullable|string|size:10',
            'subscribers.*.rank' => 'integer',
        ];
    }
}
