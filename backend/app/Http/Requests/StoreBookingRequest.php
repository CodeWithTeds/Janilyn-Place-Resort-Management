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
            'booking_type' => ['required', 'in:room,exclusive'],
            'room_type_id' => ['required_if:booking_type,room', 'nullable', 'exists:room_types,id'],
            'exclusive_resort_rental_id' => ['required_if:booking_type,exclusive', 'nullable', 'exists:exclusive_resort_rentals,id'],
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
                if ($this->has(['check_in', 'check_out'])) {
                    if ($this->booking_type === 'room' && $this->room_type_id) {
                        $this->validateRoomOverlap($validator);
                    } elseif ($this->booking_type === 'exclusive' && $this->exclusive_resort_rental_id) {
                        $this->validateExclusiveOverlap($validator);
                    }
                }
            }
        ];
    }

    protected function validateRoomOverlap(Validator $validator)
    {
        // Check overlap with other room bookings for the same room type
        $overlap = Booking::where('room_type_id', $this->room_type_id)
            ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::PENDING])
            ->where(function ($query) {
                $query->where('check_in', '<', $this->check_out)
                      ->where('check_out', '>', $this->check_in);
            })
            ->exists();

        // ALSO CHECK: If there is an exclusive rental booking during this time, we probably can't book a room
        // Assuming "Exclusive Rental" means they rent the WHOLE resort or a significant part that conflicts with individual rooms.
        // If Exclusive Rental + 3 Apartments means they take everything, then we shouldn't allow room bookings.
        // For now, let's keep it simple: check room type overlap only, unless specified otherwise.
        
        if ($overlap) {
            $validator->errors()->add(
                'room_type_id',
                'This room type is already occupied for the selected dates.'
            );
        }
    }

    protected function validateExclusiveOverlap(Validator $validator)
    {
        $overlap = Booking::where('exclusive_resort_rental_id', $this->exclusive_resort_rental_id)
            ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::PENDING])
            ->where(function ($query) {
                $query->where('check_in', '<', $this->check_out)
                      ->where('check_out', '>', $this->check_in);
            })
            ->exists();

        if ($overlap) {
            $validator->errors()->add(
                'exclusive_resort_rental_id',
                'This exclusive rental is already booked for the selected dates.'
            );
        }
    }
}
