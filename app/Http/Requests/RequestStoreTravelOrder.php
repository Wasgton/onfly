<?php

namespace App\Http\Requests;

class RequestStoreTravelOrder extends AbstractRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date|after:today',
            'return_date' => 'required|date|after:departure_date',
        ];
    }

    public function messages()
    {
        return [
            'destination.required' => 'The destination field is required.',
            'departure_date.required' => 'The departure date field is required.',
            'return_date.required' => 'The return date field is required.',
            'return_date.after' => 'The return date must be after the departure date.',
            'departure_date.after' => 'The departure date must be after today.',
        ];
    }
}
