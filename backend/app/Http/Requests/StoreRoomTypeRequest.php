<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('access-owner-dashboard');
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_package' => $this->has('is_package'),
        ]);
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
            'category' => ['nullable', 'string', 'in:DELUXE ROOM,GUEST HOUSE,APARTMENT STYLE'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'], // 2MB Max
            'min_pax' => ['required', 'integer', 'min:1'],
            'max_pax' => ['required', 'integer', 'gte:min_pax'],
            'max_day_guests' => ['nullable', 'integer', 'min:0'],
            'bedroom_count' => ['nullable', 'integer', 'min:0'],
            'base_price_weekday' => ['required', 'numeric', 'min:0'],
            'base_price_weekend' => ['required', 'numeric', 'min:0'],
            'extra_person_charge' => ['required', 'numeric', 'min:0'],
            'cooking_fee' => ['required', 'numeric', 'min:0'],
            'is_package' => ['nullable', 'boolean'],
            'amenities' => ['nullable', 'string'],
        ];
    }
}
