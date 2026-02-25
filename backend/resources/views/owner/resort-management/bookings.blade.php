<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reservation and Booking') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="bookingForm('{{ $errors->any() || request('tab') == 'walk-in' ? 'walk-in' : 'online' }}')">
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
                
                <!-- Booking Type Selection -->
                <div class="mb-6">
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" x-model="bookingType" value="room" class="form-radio text-indigo-600" name="booking_type_selector">
                            <span class="ml-2">Room Booking</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" x-model="bookingType" value="exclusive" class="form-radio text-indigo-600" name="booking_type_selector">
                            <span class="ml-2">Exclusive Rental</span>
                        </label>
                    </div>
                </div>

                <form action="{{ route('owner.resort-management.bookings.store') }}" method="POST" @submit.prevent="submitForm">
                    @csrf
                    <input type="hidden" name="booking_type" :value="bookingType">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-label for="guest_name" value="{{ __('Guest Name') }}" />
                            <x-input id="guest_name" class="block mt-1 w-full" type="text" name="guest_name" :value="old('guest_name')" x-model="formData.guest_name" required />
                            <x-input-error for="guest_name" class="mt-2" />
                            <span class="text-red-500 text-xs" x-show="errors.guest_name" x-text="errors.guest_name"></span>
                        </div>
                        
                        <!-- Room Type Select -->
                        <div x-show="bookingType === 'room'">
                            <x-label for="room_type" value="{{ __('Room Type') }}" />
                            <select id="room_type" name="room_type_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" x-model="formData.room_type_id" @change="fetchUnits()">
                                <option value="">Select Room Type</option>
                                @foreach($roomTypes as $roomType)
                                    <option value="{{ $roomType->id }}" {{ old('room_type_id') == $roomType->id ? 'selected' : '' }}>
                                        {{ $roomType->name }} 
                                        (₱{{ number_format($roomType->base_price_weekday, 2) }}/weekday, ₱{{ number_format($roomType->base_price_weekend, 2) }}/weekend)
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error for="room_type_id" class="mt-2" />
                            <span class="text-red-500 text-xs" x-show="errors.room_type_id" x-text="errors.room_type_id"></span>

                            <!-- Resort Unit Select (Optional) -->
                            <div class="mt-4" x-show="availableUnits.length > 0">
                                <x-label for="resort_unit_id" value="{{ __('Specific Unit (Optional)') }}" />
                                <select id="resort_unit_id" name="resort_unit_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" x-model="formData.resort_unit_id">
                                    <option value="">Any Available Unit</option>
                                    <template x-for="unit in availableUnits" :key="unit.id">
                                        <option :value="unit.id" x-text="unit.name"></option>
                                    </template>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Select a specific unit if preferred.</p>
                            </div>
                        </div>

                        <!-- Exclusive Rental Select -->
                        <div x-show="bookingType === 'exclusive'" style="display: none;">
                            <x-label for="exclusive_resort_rental" value="{{ __('Exclusive Rental Package') }}" />
                            <select id="exclusive_resort_rental" name="exclusive_resort_rental_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" x-model="formData.exclusive_resort_rental_id">
                                <option value="">Select Rental Package</option>
                                @foreach($exclusiveRentals as $rental)
                                    <option value="{{ $rental->id }}" {{ old('exclusive_resort_rental_id') == $rental->id ? 'selected' : '' }}>
                                        {{ $rental->name }} 
                                        (₱{{ number_format($rental->price_range_min, 2) }} - ₱{{ number_format($rental->price_range_max, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error for="exclusive_resort_rental_id" class="mt-2" />
                            <span class="text-red-500 text-xs" x-show="errors.exclusive_resort_rental_id" x-text="errors.exclusive_resort_rental_id"></span>
                        </div>

                        <div>
                            <x-label for="check_in" value="{{ __('Check-in Date') }}" />
                            <x-input id="check_in" class="block mt-1 w-full" type="date" name="check_in" :value="old('check_in')" x-model="formData.check_in" required @change="fetchUnits()" />
                            <x-input-error for="check_in" class="mt-2" />
                            <span class="text-red-500 text-xs" x-show="errors.check_in" x-text="errors.check_in"></span>
                        </div>
                        <div>
                            <x-label for="check_out" value="{{ __('Check-out Date') }}" />
                            <x-input id="check_out" class="block mt-1 w-full" type="date" name="check_out" :value="old('check_out')" x-model="formData.check_out" required @change="fetchUnits()" />
                            <x-input-error for="check_out" class="mt-2" />
                            <span class="text-red-500 text-xs" x-show="errors.check_out" x-text="errors.check_out"></span>
                        </div>
                        <div>
                            <x-label for="pax_count" value="{{ __('Pax Count') }}" />
                            <x-input id="pax_count" class="block mt-1 w-full" type="number" name="pax_count" min="1" :value="old('pax_count')" x-model="formData.pax_count" required />
                            <x-input-error for="pax_count" class="mt-2" />
                            <span class="text-red-500 text-xs" x-show="errors.pax_count" x-text="errors.pax_count"></span>
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <x-label value="{{ __('Payment Method') }}" />
                            <div class="mt-2 space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="payment_method" value="cash" class="form-radio text-indigo-600" x-model="formData.payment_method">
                                    <span class="ml-2">Cash Payment</span>
                                </label>
                                <div class="block"></div>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="payment_method" value="paymongo" class="form-radio text-indigo-600" x-model="formData.payment_method">
                                    <span class="ml-2">PayMongo (Card)</span>
                                </label>
                            </div>
                            <x-input-error for="payment_method" class="mt-2" />
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pax</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($bookings as $booking)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->guest_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->guest_email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($booking->exclusiveResortRental)
                                        <div class="text-sm text-purple-600 font-semibold">{{ $booking->exclusiveResortRental->name }}</div>
                                        <div class="text-xs text-gray-500">Exclusive Rental</div>
                                    @elseif($booking->roomType)
                                        <div class="text-sm text-gray-900">{{ $booking->roomType->name }}</div>
                                        <div class="text-xs text-gray-500">Room Booking</div>
                                    @else
                                        <div class="text-sm text-gray-500">N/A</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $booking->check_in->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $booking->check_out->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $booking->pax_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ₱{{ number_format($booking->total_price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ match($booking->payment_status?->value ?? 'unpaid') {
                                            'paid' => 'bg-green-100 text-green-800',
                                            'unpaid' => 'bg-red-100 text-red-800',
                                            'refunded' => 'bg-yellow-100 text-yellow-800',
                                            'failed' => 'bg-gray-100 text-gray-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        } }}">
                                        {{ ucfirst($booking->payment_status?->value ?? 'Unpaid') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ match($booking->status->value) {
                                            'confirmed' => 'bg-green-100 text-green-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'checked_in' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-gray-100 text-gray-800',
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
                                        <form action="{{ route('owner.resort-management.bookings.cancel', $booking) }}" method="POST" class="inline-block confirm-action"
                                              data-confirm-title="Cancel Booking?"
                                              data-confirm-text="Are you sure you want to cancel this booking?"
                                              data-confirm-icon="warning"
                                              data-confirm-button-text="Yes, cancel it!">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Cancel</button>
                                        </form>
                                    @elseif($booking->status === \App\Enums\BookingStatus::CONFIRMED)
                                        <button type="button" 
                                            @click="openCheckInModal('{{ $booking->id }}', '{{ $booking->room_type_id }}', '{{ $booking->check_in->format('Y-m-d') }}', '{{ $booking->check_out->format('Y-m-d') }}')" 
                                            class="text-green-600 hover:text-green-900 mr-3">
                                            Check In
                                        </button>
                                         <form action="{{ route('owner.resort-management.bookings.cancel', $booking) }}" method="POST" class="inline-block confirm-action"
                                               data-confirm-title="Cancel Booking?"
                                               data-confirm-text="Are you sure you want to cancel this booking?"
                                               data-confirm-icon="warning"
                                               data-confirm-button-text="Yes, cancel it!">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Cancel</button>
                                        </form>
                                    @elseif($booking->status === \App\Enums\BookingStatus::CHECKED_IN)
                                        <form action="{{ route('owner.resort-management.bookings.check-out', $booking) }}" method="POST" class="inline-block confirm-action"
                                              data-confirm-title="Check Out Guest?"
                                              data-confirm-text="Are you sure you want to check out this guest?"
                                              data-confirm-icon="info"
                                              data-confirm-button-text="Yes, check out!">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-gray-600 hover:text-gray-900 mr-3">Check Out</button>
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

    <!-- Check In Modal -->
    <div x-show="showCheckInModal" class="fixed z-10 inset-0 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form :action="checkInAction" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Check In Guest
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Please assign a unit for this booking.
                                    </p>
                                    <div class="mt-4">
                                        <div x-show="isRoomBooking">
                                            <label for="check_in_unit_id" class="block text-sm font-medium text-gray-700">Assign Unit (Required)</label>
                                            <select id="check_in_unit_id" name="resort_unit_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" :required="isRoomBooking">
                                                <option value="">Select a Unit</option>
                                                <template x-for="unit in checkInAvailableUnits" :key="unit.id">
                                                    <option :value="unit.id" x-text="unit.name"></option>
                                                </template>
                                            </select>
                                            <p x-show="checkInAvailableUnits.length === 0 && !loadingUnits" class="text-red-500 text-xs mt-1">No available units found for these dates.</p>
                                            <p x-show="loadingUnits" class="text-gray-500 text-xs mt-1">Loading available units...</p>
                                        </div>
                                        <div x-show="!isRoomBooking">
                                            <p class="text-sm text-gray-500 italic">Exclusive Rental - No specific unit assignment required.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm" :disabled="isRoomBooking && checkInAvailableUnits.length === 0">
                            Confirm Check In
                        </button>
                        <button type="button" @click="showCheckInModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function bookingForm(initialTab = 'online') {
            return {
                tab: initialTab,
                bookingType: 'room',
                formData: {
                    guest_name: '',
                    room_type_id: '',
                    exclusive_resort_rental_id: '',
                    resort_unit_id: '',
                    check_in: '',
                    check_out: '',
                    pax_count: '',
                    payment_method: 'cash'
                },
                availableUnits: [],
                errors: {},
                showCheckInModal: false,
                isRoomBooking: false,
                checkInAction: '',
                checkInAvailableUnits: [],
                loadingUnits: false,
                async openCheckInModal(bookingId, roomTypeId, checkIn, checkOut) {
                    this.checkInAction = `{{ url('owner/resort-management/bookings') }}/${bookingId}/check-in`;
                    this.showCheckInModal = true;
                    this.checkInAvailableUnits = [];
                    this.loadingUnits = true;

                    if (roomTypeId && roomTypeId !== 'null') {
                        this.isRoomBooking = true;
                        try {
                            const response = await fetch(`{{ route('owner.resort-management.bookings.available-units') }}?room_type_id=${roomTypeId}&check_in=${checkIn}&check_out=${checkOut}`);
                            if (response.ok) {
                                this.checkInAvailableUnits = await response.json();
                            }
                        } catch (error) {
                            console.error('Error fetching units:', error);
                        }
                    } else {
                        this.isRoomBooking = false;
                    }
                    this.loadingUnits = false;
                },
                async fetchUnits() {
                    if (this.bookingType === 'room' && this.formData.room_type_id && this.formData.check_in && this.formData.check_out) {
                        try {
                            const response = await fetch(`{{ route('owner.resort-management.bookings.available-units') }}?room_type_id=${this.formData.room_type_id}&check_in=${this.formData.check_in}&check_out=${this.formData.check_out}`);
                            if (response.ok) {
                                this.availableUnits = await response.json();
                            } else {
                                this.availableUnits = [];
                            }
                        } catch (error) {
                            console.error('Error fetching units:', error);
                            this.availableUnits = [];
                        }
                    } else {
                        this.availableUnits = [];
                    }
                },
                submitForm() {
                    this.errors = {};
                    let hasError = false;

                    if (!this.formData.guest_name) {
                        this.errors.guest_name = 'Guest Name is required';
                        hasError = true;
                    }

                    if (this.bookingType === 'room' && !this.formData.room_type_id) {
                        this.errors.room_type_id = 'Room Type is required';
                        hasError = true;
                    }

                    if (this.bookingType === 'exclusive' && !this.formData.exclusive_resort_rental_id) {
                        this.errors.exclusive_resort_rental_id = 'Exclusive Rental Package is required';
                        hasError = true;
                    }

                    if (!this.formData.check_in) {
                        this.errors.check_in = 'Check-in Date is required';
                        hasError = true;
                    }

                    if (!this.formData.check_out) {
                        this.errors.check_out = 'Check-out Date is required';
                        hasError = true;
                    } else if (this.formData.check_in && this.formData.check_out <= this.formData.check_in) {
                        this.errors.check_out = 'Check-out Date must be after Check-in Date';
                        hasError = true;
                    }

                    if (!this.formData.pax_count || this.formData.pax_count < 1) {
                        this.errors.pax_count = 'Pax Count must be at least 1';
                        hasError = true;
                    }

                    if (!hasError) {
                        this.$el.submit();
                    }
                }
            }
        }
    </script>
</x-app-layout>
