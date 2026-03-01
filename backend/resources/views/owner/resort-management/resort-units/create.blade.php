<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Resort Unit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('resort-management.resort-units.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div class="col-span-1 md:col-span-2">
                            <x-label for="name" value="{{ __('Unit Name/Number') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error for="name" class="mt-2" />
                        </div>

                        <!-- Room Type -->
                        <div>
                            <x-label for="room_type_id" value="{{ __('Room Type') }}" />
                            <select id="room_type_id" name="room_type_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="">Select Room Type</option>
                                @foreach($roomTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('room_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error for="room_type_id" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div>
                            <x-label for="status" value="{{ __('Status') }}" />
                            <select id="status" name="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                            </select>
                            <x-input-error for="status" class="mt-2" />
                        </div>

                        <!-- Notes -->
                        <div class="col-span-1 md:col-span-2">
                            <x-label for="notes" value="{{ __('Notes') }}" />
                            <textarea id="notes" name="notes" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="3">{{ old('notes') }}</textarea>
                            <x-input-error for="notes" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('owner.resort-management.resort-units.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <x-button class="ml-4">
                            {{ __('Create Unit') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
