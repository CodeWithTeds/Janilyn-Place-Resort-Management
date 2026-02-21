<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Room Type') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('owner.resort-management.room-types.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Left Column: Image & Basic Info -->
                        <div class="lg:col-span-1 space-y-6">
                            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 max-w-md mx-auto" x-data="{ photoName: null, photoPreview: null }">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Room Image
                                </h3>

                                <!-- Image Preview -->
                                <div class="mt-2" x-show="photoPreview" style="display: none;">
                                    <div class="relative w-full h-56 rounded-lg overflow-hidden mb-4 border border-gray-200 shadow-sm group">
                                        <img :src="photoPreview" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            <button type="button" @click.prevent="photoPreview = null; photoName = null; document.getElementById('image').value = null" class="bg-white text-red-600 rounded-full p-2 hover:bg-red-50 transition-colors duration-200 shadow-lg">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button type="button" @click.prevent="document.getElementById('image').click()" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Change Photo</button>
                                    </div>
                                </div>

                                <!-- Upload Box (Hidden when preview exists) -->
                                <div x-show="!photoPreview" class="mt-3 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors duration-200 cursor-pointer" @click.prevent="document.getElementById('image').click()">
                                    <div class="space-y-2 text-center">
                                        <div class="mx-auto h-16 w-16 text-gray-400">
                                            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <span class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500 transition-colors duration-200">
                                                <span class="font-semibold">Choose photo</span>
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                    </div>
                                </div>
                                
                                <input id="image" name="image" type="file" class="sr-only" accept="image/*"
                                    x-ref="photo"
                                    @change="
                                        photoName = $refs.photo.files[0].name;
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            photoPreview = e.target.result;
                                        };
                                        reader.readAsDataURL($refs.photo.files[0]);
                                    ">
                                <x-input-error for="image" class="mt-3" />
                            </div>

                            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 max-w-md mx-auto">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Basic Details
                                </h3>
                                
                                <div class="space-y-5">
                                    <div>
                                        <x-label for="name" value="{{ __('Room Name') }}" class="font-medium text-gray-700" />
                                        <x-input id="name" class="block mt-2 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm transition-colors duration-200" type="text" name="name" :value="old('name')" required placeholder="e.g. Ocean View Suite" />
                                        <x-input-error for="name" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-label for="category" value="{{ __('Category') }}" class="font-medium text-gray-700" />
                                        <select id="category" name="category" class="block mt-2 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm transition-colors duration-200 bg-white">
                                            <option value="" class="text-gray-500">Select Category</option>
                                            <option value="DELUXE ROOM" {{ old('category') == 'DELUXE ROOM' ? 'selected' : '' }} class="font-medium">🌟 DELUXE ROOM</option>
                                            <option value="GUEST HOUSE" {{ old('category') == 'GUEST HOUSE' ? 'selected' : '' }} class="font-medium">🏠 GUEST HOUSE</option>
                                            <option value="APARTMENT STYLE" {{ old('category') == 'APARTMENT STYLE' ? 'selected' : '' }} class="font-medium">🏢 APARTMENT STYLE</option>
                                        </select>
                                        <x-input-error for="category" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-label for="description" value="{{ __('Description') }}" class="font-medium text-gray-700" />
                                        <textarea id="description" name="description" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm block mt-2 w-full transition-colors duration-200" rows="4" placeholder="Describe the room features, view, and amenities...">{{ old('description') }}</textarea>
                                        <x-input-error for="description" class="mt-2" />
                                    </div>

                                    <div class="bg-indigo-50 p-3 rounded-lg border border-indigo-100">
                                        <label for="is_package" class="flex items-center cursor-pointer">
                                            <x-checkbox id="is_package" name="is_package" value="1" :checked="old('is_package') == 1" class="text-indigo-600 focus:ring-indigo-500" />
                                            <span class="ms-3 text-sm font-medium text-indigo-800">{{ __('Package Deal') }}</span>
                                        </label>
                                        <p class="text-xs text-indigo-600 mt-1 ml-7">Mark if this room type includes special packages</p>
                                        <x-input-error for="is_package" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Details, Pricing, Amenities -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Capacity Section -->
                            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                                <h3 class="text-lg font-semibold text-gray-800 mb-5 flex items-center">
                                    <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Capacity Details
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <x-label for="min_pax" value="{{ __('Minimum Guests') }}" class="font-medium text-gray-700 mb-2" />
                                        <x-input id="min_pax" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm transition-colors duration-200" type="number" name="min_pax" :value="old('min_pax', 1)" required min="1" placeholder="Minimum guests" />
                                        <x-input-error for="min_pax" class="mt-2" />
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <x-label for="max_pax" value="{{ __('Maximum Guests') }}" class="font-medium text-gray-700 mb-2" />
                                        <x-input id="max_pax" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm transition-colors duration-200" type="number" name="max_pax" :value="old('max_pax', 2)" required min="1" placeholder="Maximum guests" />
                                        <x-input-error for="max_pax" class="mt-2" />
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <x-label for="bedroom_count" value="{{ __('Bedrooms') }}" class="font-medium text-gray-700 mb-2" />
                                        <x-input id="bedroom_count" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm transition-colors duration-200" type="number" name="bedroom_count" :value="old('bedroom_count', 0)" min="0" placeholder="Number of bedrooms" />
                                        <x-input-error for="bedroom_count" class="mt-2" />
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <x-label for="max_day_guests" value="{{ __('Day Visitors (until 10pm)') }}" class="font-medium text-gray-700 mb-2" />
                                        <x-input id="max_day_guests" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm transition-colors duration-200" type="number" name="max_day_guests" :value="old('max_day_guests', 0)" min="0" placeholder="Maximum day visitors" />
                                        <x-input-error for="max_day_guests" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing Section -->
                            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                                <h3 class="text-lg font-semibold text-gray-800 mb-5 flex items-center">
                                    <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Pricing Configuration
                                </h3>
                                
                                <div class="space-y-5" x-data="{ 
                                    samePrice: true, 
                                    weekdayPrice: '{{ old('base_price_weekday') }}', 
                                    weekendPrice: '{{ old('base_price_weekend') }}',
                                    updatePrices() {
                                        if (this.samePrice) {
                                            this.weekendPrice = this.weekdayPrice;
                                        }
                                    }
                                }">
                                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-4 rounded-xl border border-indigo-200">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" x-model="samePrice" @change="updatePrices()" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 h-5 w-5 focus:ring-2">
                                            <span class="ms-3 text-sm font-semibold text-indigo-800">{{ __('Use same price for weekdays and weekends') }}</span>
                                        </label>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <x-label for="base_price_weekday" x-text="samePrice ? '{{ __('Base Price / Room Rate') }}' : '{{ __('Weekday Price') }}'" class="font-medium text-gray-700 mb-2" />
                                            <div class="relative mt-2">
                                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="text-gray-500 font-semibold">₱</span>
                                                </div>
                                                <input id="base_price_weekday" type="number" step="0.01" 
                                                    name="base_price_weekday" 
                                                    x-model="weekdayPrice" 
                                                    @input="updatePrices()"
                                                    class="block w-full rounded-lg border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200" 
                                                    required min="0" placeholder="0.00" />
                                            </div>
                                            <x-input-error for="base_price_weekday" class="mt-2" />
                                        </div>

                                        <div x-show="!samePrice" style="display: none;" class="bg-gray-50 p-4 rounded-lg">
                                            <x-label for="base_price_weekend" value="{{ __('Weekend Price') }}" class="font-medium text-gray-700 mb-2" />
                                            <div class="relative mt-2">
                                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="text-gray-500 font-semibold">₱</span>
                                                </div>
                                                <input id="base_price_weekend" type="number" step="0.01" 
                                                    name="base_price_weekend" 
                                                    x-model="weekendPrice" 
                                                    class="block w-full rounded-lg border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                                    required min="0" placeholder="0.00" />
                                            </div>
                                            <x-input-error for="base_price_weekend" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-4 border-t border-gray-200">
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <x-label for="extra_person_charge" value="{{ __('Extra Person Charge') }}" class="font-medium text-gray-700 mb-2" />
                                            <div class="relative mt-2">
                                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="text-gray-500 font-semibold">₱</span>
                                                </div>
                                                <input id="extra_person_charge" type="number" step="0.01" name="extra_person_charge" value="{{ old('extra_person_charge', 0) }}" class="block w-full rounded-lg border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200" required min="0" placeholder="0.00" />
                                            </div>
                                            <x-input-error for="extra_person_charge" class="mt-2" />
                                        </div>

                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <x-label for="cooking_fee" value="{{ __('Cooking Fee') }}" class="font-medium text-gray-700 mb-2" />
                                            <div class="relative mt-2">
                                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="text-gray-500 font-semibold">₱</span>
                                                </div>
                                                <input id="cooking_fee" type="number" step="0.01" name="cooking_fee" value="{{ old('cooking_fee', 0) }}" class="block w-full rounded-lg border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200" required min="0" placeholder="0.00" />
                                            </div>
                                            <x-input-error for="cooking_fee" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Amenities Section -->
                            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                                <h3 class="text-lg font-semibold text-gray-800 mb-5 flex items-center">
                                    <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                    </svg>
                                    Amenities & Features
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <x-label for="amenities" value="{{ __('Room Amenities') }}" class="font-medium text-gray-700 mb-2" />
                                        <textarea id="amenities" name="amenities" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm block mt-2 w-full transition-colors duration-200" rows="4" placeholder="WiFi, Air Conditioning, Smart TV, Hot Shower, Kitchenette, Ocean View, Private Balcony">{{ old('amenities') }}</textarea>
                                        <p class="mt-2 text-sm text-gray-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Separate amenities with commas. These will be displayed as tags on the room listing.
                                        </p>
                                        <x-input-error for="amenities" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200 bg-gray-50 p-6 rounded-xl">
                        <a href="{{ route('owner.resort-management.room-types.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-lg font-semibold text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 mr-4">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            {{ __('Cancel') }}
                        </a>
                        <x-button class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:ring-indigo-500 px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            {{ __('Create Room Type') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
