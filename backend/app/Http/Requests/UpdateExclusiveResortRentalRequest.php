<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExclusiveResortRentalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('access-owner-dashboard');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price_range_min' => ['required', 'numeric', 'min:0'],
            'price_range_max' => ['required', 'numeric', 'gte:price_range_min'],
            'capacity_overnight_min' => ['nullable', 'integer', 'min:1'],
            'capacity_overnight_max' => ['nullable', 'integer', 'gte:capacity_overnight_min'],
            'capacity_day_min' => ['nullable', 'integer', 'min:1'],
            'capacity_day_max' => ['nullable', 'integer', 'gte:capacity_day_min'],
            'cooking_fee' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'], // 2MB Max
            'features' => ['nullable', 'array'],
        ];
    }
}
