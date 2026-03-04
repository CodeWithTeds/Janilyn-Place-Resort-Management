<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reservation and Booking') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="bookingForm(
        '{{ $errors->any() || request('tab') == 'walk-in' ? 'walk-in' : 'online' }}',
        {
            booking_type: '{{ old('booking_type', 'room') }}',
            guest_name: '{{ old('guest_name', '') }}',
            room_type_id: '{{ old('room_type_id', '') }}',
            exclusive_resort_rental_id: '{{ old('exclusive_resort_rental_id', '') }}',
            resort_unit_id: '{{ old('resort_unit_id', '') }}',
            pricing_tier_id: '{{ old('pricing_tier_id', '') }}',
            check_in: '{{ old('check_in', '') }}',
            check_out: '{{ old('check_out', '') }}',
            pax_count: '{{ old('pax_count', '') }}',
            payment_method: '{{ old('payment_method', 'cash') }}'
        }
    )">
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
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" x-model="bookingType" value="room" class="form-radio text-indigo-600 h-5 w-5" name="booking_type_selector">
                            <span class="ml-2 text-gray-700 font-medium">Room Booking</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" x-model="bookingType" value="exclusive" class="form-radio text-indigo-600 h-5 w-5" name="booking_type_selector">
                            <span class="ml-2 text-gray-700 font-medium">Exclusive Rental</span>
                        </label>
                    </div>
                </div>

                <form action="{{ route('resort-management.bookings.store') }}" method="POST" @submit.prevent="submitForm">
                    @csrf
                    <input type="hidden" name="booking_type" :value="bookingType">

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Left Column: Guest & Date Details -->
                        <div class="lg:col-span-1 space-y-6">
                            <div>
                                <x-label for="guest_name" value="{{ __('Guest Name') }}" />
                                <x-input id="guest_name" class="block mt-1 w-full" type="text" name="guest_name" :value="old('guest_name')" x-model="formData.guest_name" required />
                                <x-input-error for="guest_name" class="mt-2" />
                                <span class="text-red-500 text-xs" x-show="errors.guest_name" x-text="errors.guest_name"></span>
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

                            <div x-show="bookingType === 'room' && canAddExtraPerson">
                                <x-label for="pax_count" value="{{ __('Pax Count') }}" />
                                <x-input id="pax_count" class="block mt-1 w-full" type="number" name="pax_count" min="1" :value="old('pax_count')" x-model="formData.pax_count" x-bind:required="bookingType === 'room' && canAddExtraPerson"
                                    x-bind:placeholder="maxCapacity ? `Max ${maxCapacity} guests` : 'e.g. 2'" />
                                <x-input-error for="pax_count" class="mt-2" />
                                <span class="text-red-500 text-xs" x-show="errors.pax_count" x-text="errors.pax_count"></span>
                                <p x-show="canAddExtraPerson" class="text-xs text-gray-500 mt-1 italic" style="display: none;">
                                    * You can add 1 extra person beyond standard capacity (Total Max: <span x-text="maxCapacity"></span>). Extra charges apply.
                                </p>
                            </div>

                            <div>
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

                            <!-- Total Price Display -->
                            <div class="bg-gray-50 p-4 rounded-lg" x-show="totalPrice > 0">
                                <span class="block text-sm font-medium text-gray-700">Estimated Total Price:</span>
                                <span class="block text-2xl font-bold text-indigo-600 mt-1" x-text="'₱' + totalPrice.toLocaleString('en-US', {minimumFractionDigits: 2})"></span>
                            </div>
                        </div>

                        <!-- Right Column: Room/Unit/Tier Selection -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Room Type Selection (Modern UI) -->
                            <div x-show="bookingType === 'room'">
                                <x-label value="{{ __('Select Room Type') }}" class="mb-2" />
                                <input type="hidden" name="room_type_id" x-model="formData.room_type_id">

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($roomTypes as $roomType)
                                    <div @click="formData.room_type_id = '{{ $roomType->id }}'; formData.resort_unit_id = ''; formData.pricing_tier_id = ''; fetchUnits(); fetchTiers();"
                                        class="cursor-pointer border rounded-xl p-4 transition-all duration-200 relative group"
                                        :class="formData.room_type_id == '{{ $roomType->id }}' ? 'border-indigo-500 bg-indigo-50 shadow-md ring-1 ring-indigo-500' : 'border-gray-200 bg-white hover:border-indigo-300 hover:shadow-sm'">

                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="block text-sm font-semibold text-gray-900">{{ $roomType->name }}</span>
                                                <div class="mt-1 flex flex-col space-y-0.5">
                                                    <span class="text-xs text-gray-500">
                                                        Weekday: <span class="font-medium text-gray-900">₱{{ number_format($roomType->base_price_weekday, 2) }}</span>
                                                    </span>
                                                    <span class="text-xs text-gray-500">
                                                        Weekend: <span class="font-medium text-gray-900">₱{{ number_format($roomType->base_price_weekend, 2) }}</span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div x-show="formData.room_type_id == '{{ $roomType->id }}'" class="text-indigo-600">
                                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <x-input-error for="room_type_id" class="mt-2" />
                                <span class="text-red-500 text-xs" x-show="errors.room_type_id" x-text="errors.room_type_id"></span>

                                <!-- Resort Unit Selection (Modern UI) -->
                                <div class="mt-6" x-show="availableUnits.length > 0">
                                    <x-label value="{{ __('Select Unit (Optional)') }}" class="mb-2" />
                                    <input type="hidden" name="resort_unit_id" x-model="formData.resort_unit_id">

                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                        <!-- Any Unit Option -->
                                        <div @click="formData.resort_unit_id = ''; filterTiers(); calculatePrice()"
                                            class="cursor-pointer border rounded-xl p-4 transition-all duration-200 relative group"
                                            :class="formData.resort_unit_id === '' ? 'border-indigo-500 bg-indigo-50 shadow-md ring-1 ring-indigo-500' : 'border-gray-200 bg-white hover:border-indigo-300 hover:shadow-sm'">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <div class="h-8 w-8 rounded-full flex items-center justify-center"
                                                        :class="formData.resort_unit_id === '' ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-500 group-hover:bg-indigo-50 group-hover:text-indigo-500'">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                        </svg>
                                                    </div>
                                                    <div class="ml-3">
                                                        <span class="block text-sm font-semibold text-gray-900">Any Unit</span>
                                                        <span class="block text-xs text-gray-500">Auto-assign</span>
                                                    </div>
                                                </div>
                                                <div x-show="formData.resort_unit_id === ''" class="text-indigo-600">
                                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Specific Units -->
                                        <template x-for="unit in availableUnits" :key="unit.id">
                                            <div @click="formData.resort_unit_id = unit.id; filterTiers(); calculatePrice()"
                                                class="cursor-pointer border rounded-xl p-4 transition-all duration-200 relative group"
                                                :class="formData.resort_unit_id == unit.id ? 'border-indigo-500 bg-indigo-50 shadow-md ring-1 ring-indigo-500' : 'border-gray-200 bg-white hover:border-indigo-300 hover:shadow-sm'">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <div class="h-8 w-8 rounded-full flex items-center justify-center"
                                                            :class="formData.resort_unit_id == unit.id ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-500 group-hover:bg-indigo-50 group-hover:text-indigo-500'">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                                            </svg>
                                                        </div>
                                                        <div class="ml-3">
                                                            <span class="block text-sm font-semibold text-gray-900" x-text="unit.name"></span>
                                                            <span class="block text-xs text-gray-500">Specific Unit</span>
                                                        </div>
                                                    </div>
                                                    <div x-show="formData.resort_unit_id == unit.id" class="text-indigo-600">
                                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Exclusive Rental Selection (Modern UI) -->

                            <!-- Exclusive Rental Selection (Modern UI) -->
                            <div x-show="bookingType === 'exclusive'">
                                <x-label value="{{ __('Select Rental Package') }}" class="mb-2" />
                                <input type="hidden" name="exclusive_resort_rental_id" :value="formData.exclusive_resort_rental_id">

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($exclusiveRentals as $rental)
                                    <div @click="formData.exclusive_resort_rental_id = '{{ $rental->id }}'; fetchExclusiveDetails();"
                                        class="cursor-pointer border rounded-xl p-4 transition-all duration-200 relative group"
                                        :class="formData.exclusive_resort_rental_id == '{{ $rental->id }}' ? 'border-indigo-500 bg-indigo-50 shadow-md ring-1 ring-indigo-500' : 'border-gray-200 bg-white hover:border-indigo-300 hover:shadow-sm'">

                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="block text-sm font-semibold text-gray-900">{{ $rental->name }}</span>
                                                <div class="mt-1">
                                                    <span class="text-xs text-gray-500">
                                                        Capacity: <span class="font-medium text-gray-900">{{ $rental->min_pax }} - {{ $rental->max_pax }} Pax</span>
                                                    </span>
                                                    <span class="block text-xs text-gray-500 mt-0.5">
                                                        Starts from: <span class="font-medium text-gray-900">₱{{ number_format($rental->base_price_weekday, 2) }}</span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div x-show="formData.exclusive_resort_rental_id == '{{ $rental->id }}'" class="text-indigo-600">
                                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <x-input-error for="exclusive_resort_rental_id" class="mt-2" />
                                <span class="text-red-500 text-xs" x-show="errors.exclusive_resort_rental_id" x-text="errors.exclusive_resort_rental_id"></span>
                            </div>

                            <!-- Pricing Tier Selection (Modern UI) -->
                            <div class="mt-6" x-show="bookingType === 'exclusive' && availableTiers.length === 0 && formData.exclusive_resort_rental_id">
                                <div class="rounded-md bg-yellow-50 p-4 border border-yellow-200">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800">Configuration Missing</h3>
                                            <div class="mt-2 text-sm text-yellow-700">
                                                <p>This rental package does not have any pricing tiers configured. Please edit the package to add pricing tiers.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6" x-show="availableTiers.length > 0">
                                <x-label class="mb-2" x-text="canAddExtraPerson && bookingType === 'room' ? '{{ __('Select Pricing Tier (Optional)') }}' : '{{ __('Select Pricing Tier') }}'" />
                                <input type="hidden" name="pricing_tier_id" :value="formData.pricing_tier_id">

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <!-- Auto-calculate Option (Only for Room with Extra Person) -->
                                    <div x-show="canAddExtraPerson && bookingType === 'room'" @click="formData.pricing_tier_id = ''; calculatePrice()"
                                        class="cursor-pointer border rounded-xl p-4 transition-all duration-200 relative group"
                                        :class="formData.pricing_tier_id === '' ? 'border-indigo-500 bg-indigo-50 shadow-md ring-1 ring-indigo-500' : 'border-gray-200 bg-white hover:border-indigo-300 hover:shadow-sm'">
                                        <div class="flex justify-between items-start">
                                            <div class="flex items-start">
                                                <div class="mt-0.5 h-5 w-5 rounded-full border flex items-center justify-center flex-shrink-0"
                                                    :class="formData.pricing_tier_id === '' ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300 bg-transparent'">
                                                    <div class="h-2 w-2 rounded-full bg-white" x-show="formData.pricing_tier_id === ''"></div>
                                                </div>
                                                <div class="ml-3">
                                                    <span class="block text-sm font-semibold text-gray-900">Auto-calculate</span>
                                                    <span class="block text-xs text-gray-500 mt-0.5">Best price based on pax count</span>
                                                </div>
                                            </div>
                                            <div class="text-xs font-medium text-indigo-600 bg-indigo-100 px-2 py-1 rounded-full">
                                                Recommended
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tiers -->
                                    <template x-for="tier in availableTiers" :key="tier.id">
                                        <div @click="formData.pricing_tier_id = tier.id; if(bookingType === 'exclusive' || !canAddExtraPerson) { formData.pax_count = tier.max_guests; } calculatePrice()"
                                            class="cursor-pointer border rounded-xl p-4 transition-all duration-200 relative group"
                                            :class="formData.pricing_tier_id == tier.id ? 'border-indigo-500 bg-indigo-50 shadow-md ring-1 ring-indigo-500' : 'border-gray-200 bg-white hover:border-indigo-300 hover:shadow-sm'">

                                            <div class="flex justify-between items-start">
                                                <div class="flex items-start">
                                                    <div class="mt-0.5 h-5 w-5 rounded-full border flex items-center justify-center flex-shrink-0"
                                                        :class="formData.pricing_tier_id == tier.id ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300 bg-transparent'">
                                                        <div class="h-2 w-2 rounded-full bg-white" x-show="formData.pricing_tier_id == tier.id"></div>
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="flex items-center space-x-2">
                                                            <span class="block text-sm font-semibold text-gray-900" x-text="tier.min_guests + '-' + tier.max_guests + ' Pax'"></span>
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800" x-show="tier.resort_unit_id">
                                                                Unit Specific
                                                            </span>
                                                        </div>
                                                        <div class="mt-1 flex flex-col space-y-1">
                                                            <span class="text-xs text-gray-600">
                                                                Weekday: <span class="font-medium text-gray-900" x-text="'₱' + Number(tier.price_weekday).toLocaleString()"></span>
                                                            </span>
                                                            <span class="text-xs text-gray-600">
                                                                Weekend: <span class="font-medium text-gray-900" x-text="'₱' + Number(tier.price_weekend).toLocaleString()"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <x-input-error for="pricing_tier_id" class="mt-2" />
                                <span class="text-red-500 text-xs" x-show="errors.pricing_tier_id" x-text="errors.pricing_tier_id"></span>
                            </div>
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
                    <form method="GET" action="{{ route('resort-management.bookings') }}" class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 w-full md:w-auto">
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
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="with_trashed" value="1" {{ request('with_trashed') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Include Deleted</span>
                            </label>
                            <x-button type="submit">Filter</x-button>
                            @if(request()->hasAny(['search', 'status', 'date', 'with_trashed']))
                            <a href="{{ route('resort-management.bookings') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
                                    <div class="text-xs text-gray-500">
                                        {{ $booking->roomType->category ?? 'Room Booking' }}
                                    </div>
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
                                    @if($booking->trashed())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-800 text-white">
                                            Deleted
                                        </span>
                                    @else
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
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($booking->trashed())
                                        <!-- No actions for deleted bookings yet, or restore could be added -->
                                    @elseif($booking->status === \App\Enums\BookingStatus::PENDING)
                                    <form action="{{ route('resort-management.bookings.approve', $booking) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-indigo-600 hover:text-indigo-900 mr-3">Approve</button>
                                    </form>
                                    <form action="{{ route('resort-management.bookings.cancel', $booking) }}" method="POST" class="inline-block confirm-action"
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
                                    <form action="{{ route('resort-management.bookings.cancel', $booking) }}" method="POST" class="inline-block confirm-action"
                                        data-confirm-title="Cancel Booking?"
                                        data-confirm-text="Are you sure you want to cancel this booking?"
                                        data-confirm-icon="warning"
                                        data-confirm-button-text="Yes, cancel it!">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Cancel</button>
                                    </form>
                                    @elseif($booking->status === \App\Enums\BookingStatus::CHECKED_IN)
                                    <form action="{{ route('resort-management.bookings.check-out', $booking) }}" method="POST" class="inline-block confirm-action"
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
        function bookingForm(initialTab = 'online', oldData = {}) {
            return {
                tab: initialTab,
                bookingType: oldData.booking_type || 'room',
                formData: {
                    guest_name: oldData.guest_name || '',
                    room_type_id: oldData.room_type_id || '',
                    exclusive_resort_rental_id: oldData.exclusive_resort_rental_id || '',
                    resort_unit_id: oldData.resort_unit_id || '',
                    pricing_tier_id: oldData.pricing_tier_id || '',
                    check_in: oldData.check_in || '',
                    check_out: oldData.check_out || '',
                    pax_count: oldData.pax_count || '',
                    payment_method: oldData.payment_method || 'cash'
                },
                init() {
                    this.$watch('bookingType', value => {
                        if (value === 'room') {
                            this.formData.exclusive_resort_rental_id = '';
                            this.formData.resort_unit_id = '';
                            this.availableTiers = [];
                            this.allTiers = [];
                        } else {
                            this.formData.room_type_id = '';
                            this.formData.resort_unit_id = '';
                            this.availableTiers = [];
                            this.allTiers = [];
                        }
                        this.totalPrice = 0;
                        this.errors = {};
                    });

                    if (this.bookingType === 'room' && this.formData.room_type_id) {
                        this.fetchTiers().then(() => {
                            if (this.formData.check_in && this.formData.check_out) {
                                this.fetchUnits();
                            }
                        });
                    }
                    if (this.bookingType === 'exclusive' && this.formData.exclusive_resort_rental_id) {
                        this.fetchExclusiveDetails();
                    }
                },
                availableUnits: [],
                availableTiers: [],
                allTiers: [],
                currentRoomType: null,
                currentExclusiveRental: null,
                maxCapacity: 0,
                canAddExtraPerson: false,
                totalPrice: 0,
                errors: {},
                showCheckInModal: false,
                isRoomBooking: false,
                checkInAction: '',
                checkInAvailableUnits: [],
                loadingUnits: false,
                async openCheckInModal(bookingId, roomTypeId, checkIn, checkOut) {
                    this.checkInAction = `{{ url('resort-management/bookings') }}/${bookingId}/check-in`;
                    this.showCheckInModal = true;
                    this.checkInAvailableUnits = [];
                    this.loadingUnits = true;

                    if (roomTypeId && roomTypeId !== 'null') {
                        this.isRoomBooking = true;
                        try {
                            const response = await fetch(`{{ route('resort-management.bookings.available-units') }}?room_type_id=${roomTypeId}&check_in=${checkIn}&check_out=${checkOut}`);
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
                    this.fetchTiers();
                    // If exclusive rental is selected, also re-calculate price
                    if (this.bookingType === 'exclusive' && this.formData.exclusive_resort_rental_id) {
                        this.calculatePrice();
                    }

                    if (this.bookingType === 'room' && this.formData.room_type_id && this.formData.check_in && this.formData.check_out) {
                        try {
                            const response = await fetch(`{{ route('resort-management.bookings.available-units') }}?room_type_id=${this.formData.room_type_id}&check_in=${this.formData.check_in}&check_out=${this.formData.check_out}`);
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
                    this.calculatePrice();
                },
                async fetchExclusiveDetails() {
                    if (this.formData.exclusive_resort_rental_id) {
                        try {
                            const response = await fetch(`/api/exclusive-rentals/${this.formData.exclusive_resort_rental_id}`);
                            if (response.ok) {
                                const data = await response.json();
                                this.currentExclusiveRental = data;
                                // For Exclusive Rentals, we might use tiers for capacity calculation too
                                this.allTiers = data.pricing_tiers || [];
                                this.filterTiers();
                                this.calculateCapacity();
                                this.calculatePrice();
                            }
                        } catch (error) {
                            console.error('Error fetching exclusive rental details:', error);
                        }
                    }
                },
                async fetchTiers() {
                    if (this.bookingType === 'room' && this.formData.room_type_id) {
                        try {
                            const response = await fetch(`/api/room-types/${this.formData.room_type_id}`);
                            if (response.ok) {
                                const data = await response.json();
                                this.currentRoomType = data;
                                this.allTiers = data.pricing_tiers || [];
                                this.filterTiers();
                                this.calculateCapacity();
                            } else {
                                this.allTiers = [];
                                this.availableTiers = [];
                                this.currentRoomType = null;
                                this.maxCapacity = 0;
                                this.canAddExtraPerson = false;
                            }
                        } catch (error) {
                            console.error('Error fetching tiers:', error);
                            this.allTiers = [];
                            this.availableTiers = [];
                            this.currentRoomType = null;
                        }
                    } else if (this.bookingType === 'room') {
                        this.allTiers = [];
                        this.availableTiers = [];
                        this.currentRoomType = null;
                        this.maxCapacity = 0;
                        this.canAddExtraPerson = false;
                    }
                },
                calculateCapacity() {
                    if (this.bookingType === 'exclusive' && this.currentExclusiveRental) {
                        // Use max_pax from the rental model itself (which was calculated from tiers or set manually)
                        this.maxCapacity = this.currentExclusiveRental.max_pax || 0;
                        // Or if tiers exist, double check
                        if (this.allTiers.length > 0) {
                            const maxTierCapacity = this.allTiers.reduce((max, tier) => Math.max(max, tier.max_guests), 0);
                            this.maxCapacity = Math.max(this.maxCapacity, maxTierCapacity);
                        }
                        this.canAddExtraPerson = true; // Usually exclusive rentals allow extra pax
                    } else if (this.allTiers.length > 0) {
                        const maxTierCapacity = this.allTiers.reduce((max, tier) => Math.max(max, tier.max_guests), 0);
                        const category = this.currentRoomType?.category?.toUpperCase() || '';
                        this.canAddExtraPerson = ['DELUXE ROOM', 'GUEST HOUSE'].includes(category);
                        this.maxCapacity = this.canAddExtraPerson ? maxTierCapacity + 1 : maxTierCapacity;
                    } else {
                        this.maxCapacity = 0;
                        this.canAddExtraPerson = false;
                    }
                },
                filterTiers() {
                    if (this.bookingType === 'exclusive') {
                        this.availableTiers = this.allTiers;
                        return;
                    }

                    if (this.formData.resort_unit_id) {
                    
                        this.availableTiers = this.allTiers.filter(t =>
                            t.resort_unit_id == this.formData.resort_unit_id || t.resort_unit_id === null
                        );
                    } else {
                        // If no unit selected, show only global tiers
                        this.availableTiers = this.allTiers.filter(t => t.resort_unit_id === null);
                    }
                },
                async calculatePrice() {
                    if (!this.formData.check_in || !this.formData.check_out || !this.formData.pax_count) {
                        this.totalPrice = 0;
                        return;
                    }

                    if (this.bookingType === 'room' && !this.formData.room_type_id) return;
                    if (this.bookingType === 'exclusive' && !this.formData.exclusive_resort_rental_id) return;

                    try {
                        const response = await fetch('/api/calculate-price', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                // 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // If needed, but API might be public or session based
                            },
                            body: JSON.stringify({
                                type: this.bookingType,
                                id: this.bookingType === 'room' ? this.formData.room_type_id : this.formData.exclusive_resort_rental_id,
                                check_in: this.formData.check_in,
                                check_out: this.formData.check_out,
                                pax_count: this.formData.pax_count,
                                resort_unit_id: this.formData.resort_unit_id,
                                pricing_tier_id: this.formData.pricing_tier_id
                            })
                        });

                        if (response.ok) {
                            const data = await response.json();
                            this.totalPrice = data.total_price;
                        } else {
                            console.error('Error calculating price');
                            this.totalPrice = 0;
                        }
                    } catch (error) {
                        console.error('Error calculating price:', error);
                        this.totalPrice = 0;
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

                    if (this.bookingType === 'room' && !this.canAddExtraPerson && !this.formData.pricing_tier_id) {
                        this.errors.pricing_tier_id = 'Please select a pricing tier.';
                        hasError = true;
                    }

                    if ((this.bookingType === 'exclusive' || this.canAddExtraPerson) && (!this.formData.pax_count || this.formData.pax_count < 1)) {
                        this.errors.pax_count = 'Pax Count must be at least 1';
                        hasError = true;
                    } else if (this.bookingType === 'room' && this.maxCapacity > 0 && this.formData.pax_count > this.maxCapacity) {
                        this.errors.pax_count = `Pax count cannot exceed ${this.maxCapacity} guests for this room type${this.canAddExtraPerson ? ' (including 1 extra person)' : ''}.`;
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