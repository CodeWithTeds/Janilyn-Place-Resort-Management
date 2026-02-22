<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reservation and Booking') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ tab: '{{ $errors->any() || request('tab') == 'walk-in' ? 'walk-in' : 'online' }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Tabs -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="tab = 'walk-in'" :class="{ 'border-brand-500 text-brand-600': tab === 'walk-in', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'walk-in' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Walk-in Booking
                    </button>
                    <button @click="tab = 'online'" :class="{ 'border-brand-500 text-brand-600': tab === 'online', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'online' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Bookings List
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

            <!-- Bookings List -->
            <div x-show="tab === 'online'" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6" x-cloak>
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
                    <h3 class="text-lg font-medium text-gray-900">All Bookings</h3>
                    
                    <!-- Filters -->
                    <form method="GET" action="{{ route('owner.resort-management.bookings') }}" class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 w-full md:w-auto">
                        <input type="hidden" name="tab" value="online"> <!-- Keep tab active if possible, though this is JS based. We might need to persist tab state via URL param or just default to online if filters are present? -->
                        <!-- Actually, if we submit form, page reloads. JS defaults to 'walk-in'. We should fix that. -->
                        
                        <div>
                            <x-input type="text" name="search" placeholder="Search Guest..." value="{{ request('search') }}" class="w-full md:w-48" />
                        </div>
                        <div>
                            <select name="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full md:w-40">
                                <option value="">All Status</option>
                                @foreach(\App\Enums\BookingStatus::cases() as $status)
                                    <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                                        {{ ucfirst($status->value) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input type="date" name="date" value="{{ request('date') }}" class="w-full md:w-40" />
                        </div>
                        <div class="flex space-x-2">
                            <x-button type="submit">Filter</x-button>
                            @if(request()->hasAny(['search', 'status', 'date']))
                                <a href="{{ route('owner.resort-management.bookings') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pax</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Occupancy</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($bookings as $booking)
                            <tr class="{{ \Carbon\Carbon::parse($booking->check_in)->isToday() ? 'bg-indigo-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->guest_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->guest_email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $booking->roomType->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}
                                    @if(\Carbon\Carbon::parse($booking->check_in)->isToday())
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Today</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $booking->pax_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">₱{{ number_format($booking->total_price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $status = $booking->status;
                                        $checkIn = \Carbon\Carbon::parse($booking->check_in)->startOfDay();
                                        $checkOut = \Carbon\Carbon::parse($booking->check_out)->startOfDay();
                                        $today = \Carbon\Carbon::today();

                                        if ($status === \App\Enums\BookingStatus::CANCELLED) {
                                            $occLabel = 'Available';
                                            $occColor = 'bg-green-100 text-green-800';
                                        } elseif ($status === \App\Enums\BookingStatus::COMPLETED) {
                                            $occLabel = 'Vacated';
                                            $occColor = 'bg-gray-100 text-gray-800';
                                        } elseif ($status === \App\Enums\BookingStatus::PENDING) {
                                            $occLabel = 'Pending';
                                            $occColor = 'bg-yellow-100 text-yellow-800';
                                        } elseif ($status === \App\Enums\BookingStatus::CHECKED_IN) {
                                            $occLabel = 'Occupied';
                                            $occColor = 'bg-indigo-100 text-indigo-800';
                                        } elseif ($status === \App\Enums\BookingStatus::CONFIRMED) {
                                            if ($today->lt($checkIn)) {
                                                $occLabel = 'Reserved';
                                                $occColor = 'bg-blue-100 text-blue-800';
                                            } elseif ($today->eq($checkIn)) {
                                                $occLabel = 'Arriving';
                                                $occColor = 'bg-green-100 text-green-800';
                                            } elseif ($today->gt($checkIn) && $today->lt($checkOut)) {
                                                $occLabel = 'Occupied';
                                                $occColor = 'bg-red-100 text-red-800';
                                            } elseif ($today->eq($checkOut)) {
                                                $occLabel = 'Due Out';
                                                $occColor = 'bg-orange-100 text-orange-800';
                                            } else {
                                                $occLabel = 'Overstay';
                                                $occColor = 'bg-red-100 text-red-800';
                                            }
                                        } else {
                                            $occLabel = 'Unknown';
                                            $occColor = 'bg-gray-100 text-gray-800';
                                        }
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $occColor }}">
                                        {{ $occLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ match($booking->status->value) {
                                            'confirmed' => 'bg-green-100 text-green-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'completed' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        } }}">
                                        {{ ucfirst($booking->status->value) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($booking->status === \App\Enums\BookingStatus::PENDING)
                                        <form action="{{ route('owner.resort-management.bookings.approve', $booking) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-indigo-600 hover:text-indigo-900 mr-3">Approve</button>
                                        </form>
                                        <form action="{{ route('owner.resort-management.bookings.cancel', $booking) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</button>
                                        </form>
                                    @elseif($booking->status === \App\Enums\BookingStatus::CONFIRMED)
                                        <form action="{{ route('owner.resort-management.bookings.check-in', $booking) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-900 mr-3" onclick="return confirm('Check in this guest?')">Check In</button>
                                        </form>
                                         <form action="{{ route('owner.resort-management.bookings.cancel', $booking) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</button>
                                        </form>
                                    @elseif($booking->status === \App\Enums\BookingStatus::CHECKED_IN)
                                        <form action="{{ route('owner.resort-management.bookings.check-out', $booking) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-gray-600 hover:text-gray-900 mr-3" onclick="return confirm('Check out this guest?')">Check Out</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                    No bookings found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $bookings->withQueryString()->links() }}
                </div>
            </div>


        </div>
    </div>
</x-app-layout>
