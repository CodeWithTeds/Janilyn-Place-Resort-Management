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
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cooking_fee' => ['nullable', 'numeric', 'min:0'],
            'extra_person_charge' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'], // 2MB Max
            'features' => ['nullable', 'array'],
            'pricing_tiers' => ['nullable', 'array'], // Can be null if not changing tiers? Actually better to always require if the UI sends it. But RoomType logic handles re-creation.
            'pricing_tiers.*.min_guests' => ['required_with:pricing_tiers', 'integer', 'min:1'],
            'pricing_tiers.*.max_guests' => ['required_with:pricing_tiers', 'integer', 'gte:pricing_tiers.*.min_guests'],
            'pricing_tiers.*.price_weekday' => ['required_with:pricing_tiers', 'numeric', 'min:0'],
            'pricing_tiers.*.price_weekend' => ['required_with:pricing_tiers', 'numeric', 'min:0'],
        ];
    }
}
