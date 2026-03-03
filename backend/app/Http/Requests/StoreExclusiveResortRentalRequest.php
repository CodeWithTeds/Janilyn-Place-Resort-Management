<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExclusiveResortRentalRequest extends FormRequest
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
            'cooking_fee' => ['nullable', 'numeric', 'min:0'],
            'extra_person_charge' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'], // 2MB Max
            'features' => ['nullable', 'array'],
            'pricing_tiers' => ['required', 'array', 'min:1'],
            'pricing_tiers.*.min_guests' => ['required', 'integer', 'min:1'],
            'pricing_tiers.*.max_guests' => ['required', 'integer', 'gte:pricing_tiers.*.min_guests'],
            'pricing_tiers.*.price_weekday' => ['required', 'numeric', 'min:0'],
            'pricing_tiers.*.price_weekend' => ['required', 'numeric', 'min:0'],
        ];
    }
}
