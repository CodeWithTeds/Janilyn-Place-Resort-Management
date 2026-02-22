<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Booking;
use App\Enums\BookingStatus;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
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
            'guest_name' => ['required', 'string', 'max:255'],
            'room_type_id' => ['required', 'exists:room_types,id'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'pax_count' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->has(['room_type_id', 'check_in', 'check_out'])) {
                    $overlap = Booking::where('room_type_id', $this->room_type_id)
                        ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::PENDING])
                        ->where(function ($query) {
                            $query->where('check_in', '<', $this->check_out)
                                  ->where('check_out', '>', $this->check_in);
                        })
                        ->exists();

                    if ($overlap) {
                        $validator->errors()->add(
                            'room_type_id',
                            'This room type is already occupied for the selected dates.'
                        );
                    }
                }
            }
        ];
    }
}
