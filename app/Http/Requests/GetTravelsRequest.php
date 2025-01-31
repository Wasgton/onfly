<?php

namespace App\Http\Requests;

use App\Enum\TravelOrderStatus;
use Illuminate\Validation\Rules\Enum;

class GetTravelsRequest extends AbstractRequest
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
            'create_period_start' => 'nullable|date|required_with:create_period_end|before:create_period_end',
            'create_period_end' => 'nullable|date|required_with:create_period_start|after:create_period_start',
            'departure_period_start' => 'nullable|date|required_with:departure_period_end|before:departure_period_end',
            'departure_period_end' => 'nullable|date|required_with:departure_period_start|after:departure_period_start',            
            'destination' => 'nullable|string',
            'status' => [
                'nullable',
                new Enum(TravelOrderStatus::class),
            ],
            'applicant_name' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [            
            'create_period_start.required_with' => 'The start date is required when the end date is present.',
            'create_period_start.date'          => 'The start date must be a valid date.',
            'create_period_start.before'        => 'The start date must be before the end date.',
            'create_period_end.required_with'   => 'The end date is required when the start date is present.',
            'create_period_end.date'            => 'The end date must be a valid date.',
            'create_period_end.after'           => 'The end date must be after the start date.',
            'departure_period_start.required_with' => 'The start date is required when the end date is present.',
            'departure_period_start.date'          => 'The start date must be a valid date.',
            'departure_period_start.before'        => 'The start date must be before the end date.',
            'departure_period_end.required_with'   => 'The end date is required when the start date is present.',
            'departure_period_end.date'            => 'The end date must be a valid date.',
            'departure_period_end.after'           => 'The end date must be after the start date.',
            'destination.string'         => 'The destination must be a valid string.',
            'status.enum' => 'Status is not valid. Valid values are: ' . implode(
                    ', ',
                    array_map(static fn($case) => $case->name, TravelOrderStatus::cases())
                ) . '.',
            'applicant_name.string'      => 'The applicant name must be a valid string.',
        ];
    }
}
