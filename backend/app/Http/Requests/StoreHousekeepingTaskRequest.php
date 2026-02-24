<?php

namespace App\Http\Requests;

use App\Enums\HousekeepingPriority;
use App\Enums\HousekeepingStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHousekeepingTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('access-owner-dashboard');
    }

    public function rules(): array
    {
        return [
            'resort_unit_id' => ['required', 'exists:resort_units,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::enum(HousekeepingStatus::class)],
            'priority' => ['required', Rule::enum(HousekeepingPriority::class)],
            'due_date' => ['required', 'date'],
        ];
    }
}
