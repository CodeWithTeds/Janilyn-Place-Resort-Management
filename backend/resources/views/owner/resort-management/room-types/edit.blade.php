<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Room Type') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('owner.resort-management.room-types.update', $roomType) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Left Column: Image & Basic Info -->
                        <div class="lg:col-span-1 space-y-6">
                            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 max-w-md mx-auto" x-data="{ photoName: null, photoPreview: '{{ $roomType->image_path ? Storage::url($roomType->image_path) : '' }}' }">
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
                                        <x-input id="name" class="block mt-2 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm transition-colors duration-200" type="text" name="name" :value="old('name', $roomType->name)" required placeholder="e.g. Deluxe Room" />
                                        <x-input-error for="name" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-label for="category" value="{{ __('Category') }}" class="font-medium text-gray-700" />
                                        <select id="category" name="category" class="block mt-2 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm transition-colors duration-200">
                                            <option value="" disabled>Select a category</option>
                                            <option value="DELUXE ROOM" {{ old('category', $roomType->category) == 'DELUXE ROOM' ? 'selected' : '' }}>DELUXE ROOM</option>
                                            <option value="APARTMENT STYLE" {{ old('category', $roomType->category) == 'APARTMENT STYLE' ? 'selected' : '' }}>APARTMENT STYLE</option>
                                            <option value="GUEST HOUSE" {{ old('category', $roomType->category) == 'GUEST HOUSE' ? 'selected' : '' }}>GUEST HOUSE</option>
                                            <option value="Others" {{ old('category', $roomType->category) == 'Others' ? 'selected' : '' }}>Others</option>
                                        </select>
                                        <x-input-error for="category" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-label for="description" value="{{ __('Description') }}" class="font-medium text-gray-700" />
                                        <textarea id="description" name="description" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm block mt-2 w-full transition-colors duration-200" rows="3" placeholder="Room description...">{{ old('description', $roomType->description) }}</textarea>
                                        <x-input-error for="description" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-label for="bedroom_count" value="{{ __('Bedroom Count') }}" class="font-medium text-gray-700" />
                                        <x-input id="bedroom_count" class="block mt-2 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" type="number" name="bedroom_count" :value="old('bedroom_count', $roomType->bedroom_count)" min="0" placeholder="1" />
                                        <x-input-error for="bedroom_count" class="mt-2" />
                                    </div>

                                    <div>
                                        <label for="is_package" class="flex items-center">
                                            <x-checkbox id="is_package" name="is_package" :checked="old('is_package', $roomType->is_package) == 1" />
                                            <span class="ml-2 text-sm text-gray-600">{{ __('Is Package Deal?') }}</span>
                                        </label>
                                        <x-input-error for="is_package" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Pricing & Capacity -->
                        <div class="lg:col-span-2 space-y-6">
                            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Pricing Configuration
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <x-label for="base_price_weekday" value="{{ __('Base Price (Weekday) ₱') }}" class="font-medium text-gray-700" />
                                        <x-input id="base_price_weekday" class="block mt-2 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" type="number" step="0.01" name="base_price_weekday" :value="old('base_price_weekday', $roomType->base_price_weekday)" required placeholder="0.00" />
                                        <x-input-error for="base_price_weekday" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-label for="base_price_weekend" value="{{ __('Base Price (Weekend) ₱') }}" class="font-medium text-gray-700" />
                                        <x-input id="base_price_weekend" class="block mt-2 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" type="number" step="0.01" name="base_price_weekend" :value="old('base_price_weekend', $roomType->base_price_weekend)" required placeholder="0.00" />
                                        <x-input-error for="base_price_weekend" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-label for="extra_person_charge" value="{{ __('Extra Person Charge (₱)') }}" class="font-medium text-gray-700" />
                                        <x-input id="extra_person_charge" class="block mt-2 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" type="number" step="0.01" name="extra_person_charge" :value="old('extra_person_charge', $roomType->extra_person_charge)" placeholder="0.00" />
                                        <x-input-error for="extra_person_charge" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-label for="cooking_fee" value="{{ __('Cooking Fee (₱)') }}" class="font-medium text-gray-700" />
                                        <x-input id="cooking_fee" class="block mt-2 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" type="number" step="0.01" name="cooking_fee" :value="old('cooking_fee', $roomType->cooking_fee)" placeholder="0.00" />
                                        <x-input-error for="cooking_fee" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Capacity Settings
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <x-label for="min_pax" value="{{ __('Minimum Pax') }}" class="font-medium text-gray-700" />
                                        <x-input id="min_pax" class="block mt-2 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" type="number" name="min_pax" :value="old('min_pax', $roomType->min_pax)" required min="1" placeholder="1" />
                                        <x-input-error for="min_pax" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-label for="max_pax" value="{{ __('Maximum Pax') }}" class="font-medium text-gray-700" />
                                        <x-input id="max_pax" class="block mt-2 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" type="number" name="max_pax" :value="old('max_pax', $roomType->max_pax)" required min="1" placeholder="2" />
                                        <x-input-error for="max_pax" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-label for="max_day_guests" value="{{ __('Max Day Guests') }}" class="font-medium text-gray-700" />
                                        <x-input id="max_day_guests" class="block mt-2 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" type="number" name="max_day_guests" :value="old('max_day_guests', $roomType->max_day_guests)" min="0" placeholder="0" />
                                        <x-input-error for="max_day_guests" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    Amenities
                                </h3>

                                <div>
                                    <x-label for="amenities" value="{{ __('Amenities (Separate by comma)') }}" class="font-medium text-gray-700" />
                                    <textarea id="amenities" name="amenities" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm block mt-2 w-full transition-colors duration-200" rows="2" placeholder="e.g. WiFi, AC, TV">{{ old('amenities', $roomType->amenities) }}</textarea>
                                    <x-input-error for="amenities" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-4 pt-4 border-t border-gray-100">
                                <a href="{{ route('owner.resort-management.room-types.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-lg font-semibold text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 mr-4">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    {{ __('Cancel') }}
                                </a>
                                <x-button class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:ring-indigo-500 px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    {{ __('Update Room Type') }}
                                </x-button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>