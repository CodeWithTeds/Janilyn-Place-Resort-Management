<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Room Type') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('owner.resort-management.room-types.update', $roomType) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="name" value="{{ __('Name') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $roomType->name)" required autofocus />
                            <x-input-error for="name" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="description" value="{{ __('Description') }}" />
                            <textarea id="description" name="description" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="3">{{ old('description', $roomType->description) }}</textarea>
                            <x-input-error for="description" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <label for="is_package" class="flex items-center">
                                <x-checkbox id="is_package" name="is_package" value="1" :checked="old('is_package', $roomType->is_package) == 1" />
                                <span class="ms-2 text-sm text-gray-600">{{ __('Is this a Package?') }}</span>
                            </label>
                            <x-input-error for="is_package" class="mt-2" />
                        </div>

                        <!-- Capacity -->
                        <div class="md:col-span-2 mt-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Capacity Details</h3>
                        </div>

                        <div>
                            <x-label for="min_pax" value="{{ __('Minimum Pax') }}" />
                            <x-input id="min_pax" class="block mt-1 w-full" type="number" name="min_pax" :value="old('min_pax', $roomType->min_pax)" required min="1" />
                            <x-input-error for="min_pax" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="max_pax" value="{{ __('Maximum Pax') }}" />
                            <x-input id="max_pax" class="block mt-1 w-full" type="number" name="max_pax" :value="old('max_pax', $roomType->max_pax)" required min="1" />
                            <x-input-error for="max_pax" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="bedroom_count" value="{{ __('Bedroom Count') }}" />
                            <x-input id="bedroom_count" class="block mt-1 w-full" type="number" name="bedroom_count" :value="old('bedroom_count', $roomType->bedroom_count)" min="0" />
                            <x-input-error for="bedroom_count" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="max_day_guests" value="{{ __('Max Day Guests (until 10pm)') }}" />
                            <x-input id="max_day_guests" class="block mt-1 w-full" type="number" name="max_day_guests" :value="old('max_day_guests', $roomType->max_day_guests)" min="0" />
                            <x-input-error for="max_day_guests" class="mt-2" />
                        </div>

                        <!-- Pricing -->
                        <div class="md:col-span-2 mt-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing</h3>
                        </div>

                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{ 
                            samePrice: {{ old('base_price_weekday', $roomType->base_price_weekday) == old('base_price_weekend', $roomType->base_price_weekend) ? 'true' : 'false' }}, 
                            weekdayPrice: '{{ old('base_price_weekday', $roomType->base_price_weekday) }}', 
                            weekendPrice: '{{ old('base_price_weekend', $roomType->base_price_weekend) }}',
                            updatePrices() {
                                if (this.samePrice) {
                                    this.weekendPrice = this.weekdayPrice;
                                }
                            }
                        }">
                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="samePrice" @change="updatePrices()" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ms-2 text-sm text-gray-600">{{ __('Same price for Weekdays and Weekends') }}</span>
                                </label>
                            </div>

                            <div>
                                <x-label for="base_price_weekday" x-text="samePrice ? '{{ __('Base Price / Room Rate') }}' : '{{ __('Weekday Price') }}'" />
                                <x-input id="base_price_weekday" class="block mt-1 w-full" type="number" step="0.01" 
                                    name="base_price_weekday" 
                                    x-model="weekdayPrice" 
                                    @input="updatePrices()"
                                    required min="0" />
                                <x-input-error for="base_price_weekday" class="mt-2" />
                            </div>

                            <div x-show="!samePrice" style="display: none;">
                                <x-label for="base_price_weekend" value="{{ __('Weekend Price') }}" />
                                <x-input id="base_price_weekend" class="block mt-1 w-full" type="number" step="0.01" 
                                    name="base_price_weekend" 
                                    x-model="weekendPrice" 
                                    required min="0" />
                                <x-input-error for="base_price_weekend" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-label for="extra_person_charge" value="{{ __('Extra Person Charge') }}" />
                            <x-input id="extra_person_charge" class="block mt-1 w-full" type="number" step="0.01" name="extra_person_charge" :value="old('extra_person_charge', $roomType->extra_person_charge)" required min="0" />
                            <x-input-error for="extra_person_charge" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="cooking_fee" value="{{ __('Cooking Fee') }}" />
                            <x-input id="cooking_fee" class="block mt-1 w-full" type="number" step="0.01" name="cooking_fee" :value="old('cooking_fee', $roomType->cooking_fee)" required min="0" />
                            <x-input-error for="cooking_fee" class="mt-2" />
                        </div>

                        <!-- Other -->
                        <div class="md:col-span-2 mt-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Other Details</h3>
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="amenities" value="{{ __('Amenities (Comma separated)') }}" />
                            <textarea id="amenities" name="amenities" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="3" placeholder="WiFi, AC, TV, etc.">{{ old('amenities', $roomType->amenities) }}</textarea>
                            <x-input-error for="amenities" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('owner.resort-management.room-types.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                            {{ __('Cancel') }}
                        </a>
                        <x-button>
                            {{ __('Update Room Type') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
