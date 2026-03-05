<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Booking Summary') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Review Booking Details</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Guest Details -->
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">Guest Information</h4>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <p><span class="text-gray-500">Name:</span> {{ $data['guest_name'] }}</p>
                            @if(!empty($data['payment_method']))
                            <p><span class="text-gray-500">Payment Method:</span> {{ ucfirst($data['payment_method']) }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">Reservation Details</h4>
                        <div class="bg-gray-50 p-4 rounded-md space-y-2">
                            <p><span class="text-gray-500">Type:</span> {{ $data['booking_type'] === 'room' ? 'Room Booking' : 'Exclusive Rental' }}</p>
                            @if($roomType)
                                <p><span class="text-gray-500">Room:</span> {{ $roomType->name }}</p>
                            @endif
                            @if($rental)
                                <p><span class="text-gray-500">Package:</span> {{ $rental->name }}</p>
                            @endif
                            @if($unit)
                                <p><span class="text-gray-500">Unit:</span> {{ $unit->name }}</p>
                            @endif
                            @if($tier)
                                <p><span class="text-gray-500">Tier:</span> {{ $tier->min_guests }}-{{ $tier->max_guests }} Pax</p>
                            @endif
                            <p><span class="text-gray-500">Dates:</span> {{ \Carbon\Carbon::parse($data['check_in'])->format('M d, Y') }} - {{ \Carbon\Carbon::parse($data['check_out'])->format('M d, Y') }}</p>
                            <p><span class="text-gray-500">Duration:</span> {{ \Carbon\Carbon::parse($data['check_in'])->diffInDays(\Carbon\Carbon::parse($data['check_out'])) }} Nights</p>
                            <p><span class="text-gray-500">Guests:</span> {{ $data['pax_count'] }} Pax</p>
                        </div>
                    </div>
                </div>

                <!-- Price Breakdown -->
                <div class="mt-8 border-t pt-6">
                    <h4 class="font-semibold text-gray-700 mb-4">Price Breakdown</h4>
                    <div class="bg-gray-50 p-6 rounded-md">
                        @php
                            $cookingFee = 0;
                            if (!empty($data['has_cooking_fee'])) {
                                $cookingFee = $roomType->cooking_fee ?? $rental->cooking_fee ?? 0;
                            }
                            $basePrice = $totalPrice - $cookingFee;
                        @endphp

                        <div class="flex justify-between mb-2">
                            <span>Accommodation & Fees</span>
                            <span>₱{{ number_format($basePrice, 2) }}</span>
                        </div>
                        
                        @if(!empty($data['has_cooking_fee']))
                        <div class="flex justify-between mb-2 text-indigo-600">
                            <span>Cooking Fee (Add-on)</span>
                            <span>₱{{ number_format($cookingFee, 2) }}</span>
                        </div>
                        @endif

                        <div class="flex justify-between mt-4 pt-4 border-t border-gray-200 font-bold text-lg">
                            <span>Total Amount</span>
                            <span class="text-indigo-600">₱{{ number_format($totalPrice, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-8 flex justify-end space-x-4">
                    <button onclick="window.history.back()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Edit Booking
                    </button>
                    
                    <form action="{{ route('resort-management.bookings.store') }}" method="POST">
                        @csrf
                        @foreach($data as $key => $value)
                            @if($key !== 'has_cooking_fee')
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <!-- Handle boolean properly for hidden input -->
                        <input type="hidden" name="has_cooking_fee" value="{{ !empty($data['has_cooking_fee']) ? 1 : 0 }}">
                        
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Confirm & Pay
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
