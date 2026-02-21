<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reservation and Booking') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ tab: 'walk-in' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Tabs -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="tab = 'walk-in'" :class="{ 'border-brand-500 text-brand-600': tab === 'walk-in', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'walk-in' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Walk-in Booking
                    </button>
                    <button @click="tab = 'online'" :class="{ 'border-brand-500 text-brand-600': tab === 'online', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'online' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Online Reservations
                    </button>
                </nav>
            </div>

            <!-- Walk-in Booking Form -->
            <div x-show="tab === 'walk-in'" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">New Walk-in Reservation</h3>
                <form action="{{ route('owner.resort-management.bookings.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-label for="guest_name" value="{{ __('Guest Name') }}" />
                            <x-input id="guest_name" class="block mt-1 w-full" type="text" name="guest_name" :value="old('guest_name')" required />
                            <x-input-error for="guest_name" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="room_type" value="{{ __('Room Type') }}" />
                            <select id="room_type" name="room_type_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                <option value="">Select Room Type</option>
                                @foreach($roomTypes as $roomType)
                                    <option value="{{ $roomType->id }}" {{ old('room_type_id') == $roomType->id ? 'selected' : '' }}>
                                        {{ $roomType->name }} 
                                        (₱{{ number_format($roomType->base_price_weekday, 2) }}/weekday, ₱{{ number_format($roomType->base_price_weekend, 2) }}/weekend)
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error for="room_type_id" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="check_in" value="{{ __('Check-in Date') }}" />
                            <x-input id="check_in" class="block mt-1 w-full" type="date" name="check_in" :value="old('check_in')" required />
                            <x-input-error for="check_in" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="check_out" value="{{ __('Check-out Date') }}" />
                            <x-input id="check_out" class="block mt-1 w-full" type="date" name="check_out" :value="old('check_out')" required />
                            <x-input-error for="check_out" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="pax_count" value="{{ __('Pax Count') }}" />
                            <x-input id="pax_count" class="block mt-1 w-full" type="number" name="pax_count" min="1" :value="old('pax_count')" required />
                            <x-input-error for="pax_count" class="mt-2" />
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <x-button>
                            {{ __('Create Booking') }}
                        </x-button>
                    </div>
                </form>
            </div>

            <!-- Online Reservations List -->
            <div x-show="tab === 'online'" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6" x-cloak>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pending Online Reservations</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pax</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($pendingBookings as $booking)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $booking->guest_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $booking->roomType->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($booking->check_in)->format('M d') }} - 
                                    {{ \Carbon\Carbon::parse($booking->check_out)->format('M d') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $booking->pax_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">₱{{ number_format($booking->total_price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $booking->status === \App\Enums\BookingStatus::CONFIRMED ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $booking->status === \App\Enums\BookingStatus::PENDING ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $booking->status === \App\Enums\BookingStatus::CANCELLED ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $booking->status === \App\Enums\BookingStatus::COMPLETED ? 'bg-gray-100 text-gray-800' : '' }}
                                    ">
                                        {{ ucfirst($booking->status->value) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form action="{{ route('owner.resort-management.bookings.approve', $booking) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-indigo-600 hover:text-indigo-900 mr-3">Approve</button>
                                    </form>
                                    <form action="{{ route('owner.resort-management.bookings.cancel', $booking) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Reject</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No pending reservations found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
