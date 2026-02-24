<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Room Type') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('owner.resort-management.room-types.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <x-label for="name" value="{{ __('Name') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error for="name" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div>
                            <x-label for="category" value="{{ __('Category') }}" />
                            <x-input id="category" class="block mt-1 w-full" type="text" name="category" :value="old('category')" />
                            <x-input-error for="category" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="col-span-1 md:col-span-2">
                            <x-label for="description" value="{{ __('Description') }}" />
                            <textarea id="description" name="description" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="3">{{ old('description') }}</textarea>
                            <x-input-error for="description" class="mt-2" />
                        </div>

                        <!-- Image -->
                        <div class="col-span-1 md:col-span-2">
                            <x-label for="image" value="{{ __('Image') }}" />
                            <input id="image" type="file" name="image" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                            <x-input-error for="image" class="mt-2" />
                        </div>

                        <!-- Pax Settings -->
                        <div>
                            <x-label for="min_pax" value="{{ __('Minimum Pax') }}" />
                            <x-input id="min_pax" class="block mt-1 w-full" type="number" name="min_pax" :value="old('min_pax', 1)" required min="1" />
                            <x-input-error for="min_pax" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="max_pax" value="{{ __('Maximum Pax') }}" />
                            <x-input id="max_pax" class="block mt-1 w-full" type="number" name="max_pax" :value="old('max_pax', 2)" required min="1" />
                            <x-input-error for="max_pax" class="mt-2" />
                        </div>

                        <!-- Pricing -->
                        <div>
                            <x-label for="base_price_weekday" value="{{ __('Base Price (Weekday)') }}" />
                            <x-input id="base_price_weekday" class="block mt-1 w-full" type="number" step="0.01" name="base_price_weekday" :value="old('base_price_weekday')" required />
                            <x-input-error for="base_price_weekday" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="base_price_weekend" value="{{ __('Base Price (Weekend)') }}" />
                            <x-input id="base_price_weekend" class="block mt-1 w-full" type="number" step="0.01" name="base_price_weekend" :value="old('base_price_weekend')" required />
                            <x-input-error for="base_price_weekend" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="extra_person_charge" value="{{ __('Extra Person Charge') }}" />
                            <x-input id="extra_person_charge" class="block mt-1 w-full" type="number" step="0.01" name="extra_person_charge" :value="old('extra_person_charge', 0)" />
                            <x-input-error for="extra_person_charge" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="cooking_fee" value="{{ __('Cooking Fee') }}" />
                            <x-input id="cooking_fee" class="block mt-1 w-full" type="number" step="0.01" name="cooking_fee" :value="old('cooking_fee', 0)" />
                            <x-input-error for="cooking_fee" class="mt-2" />
                        </div>

                        <!-- Additional Details -->
                        <div>
                            <x-label for="bedroom_count" value="{{ __('Bedroom Count') }}" />
                            <x-input id="bedroom_count" class="block mt-1 w-full" type="number" name="bedroom_count" :value="old('bedroom_count')" min="0" />
                            <x-input-error for="bedroom_count" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="max_day_guests" value="{{ __('Max Day Guests') }}" />
                            <x-input id="max_day_guests" class="block mt-1 w-full" type="number" name="max_day_guests" :value="old('max_day_guests')" min="0" />
                            <x-input-error for="max_day_guests" class="mt-2" />
                        </div>

                        <!-- Amenities -->
                        <div class="col-span-1 md:col-span-2">
                            <x-label for="amenities" value="{{ __('Amenities (Separate by comma)') }}" />
                            <textarea id="amenities" name="amenities" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="2">{{ old('amenities') }}</textarea>
                            <x-input-error for="amenities" class="mt-2" />
                        </div>

                        <!-- Is Package -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="is_package" class="flex items-center">
                                <x-checkbox id="is_package" name="is_package" :checked="old('is_package') == 1" />
                                <span class="ml-2 text-sm text-gray-600">{{ __('Is Package Deal?') }}</span>
                            </label>
                            <x-input-error for="is_package" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('owner.resort-management.room-types.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <x-button class="ml-4">
                            {{ __('Create Room Type') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
