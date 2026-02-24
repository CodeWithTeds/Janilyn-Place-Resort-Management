<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Room Inspection') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('owner.room-inspections.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-1 md:col-span-2">
                            <x-label for="resort_unit_id" value="{{ __('Resort Unit') }}" />
                            <select id="resort_unit_id" name="resort_unit_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="">Select a Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('resort_unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="resort_unit_id" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="type" value="{{ __('Inspection Type') }}" />
                            <select id="type" name="type" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="Routine" {{ old('type') == 'Routine' ? 'selected' : '' }}>Routine Check</option>
                                <option value="Check-in" {{ old('type') == 'Check-in' ? 'selected' : '' }}>Check-in Inspection</option>
                                <option value="Check-out" {{ old('type') == 'Check-out' ? 'selected' : '' }}>Check-out Inspection</option>
                                <option value="Deep Clean" {{ old('type') == 'Deep Clean' ? 'selected' : '' }}>Deep Clean Verification</option>
                            </select>
                            <x-input-error for="type" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="status" value="{{ __('Result / Status') }}" />
                            <select id="status" name="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="Passed" {{ old('status') == 'Passed' ? 'selected' : '' }}>Passed</option>
                                <option value="Needs Cleaning" {{ old('status') == 'Needs Cleaning' ? 'selected' : '' }}>Needs Cleaning</option>
                                <option value="Failed" {{ old('status') == 'Failed' ? 'selected' : '' }}>Failed (Issues Found)</option>
                            </select>
                            <x-input-error for="status" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-label for="notes" value="{{ __('Inspection Notes') }}" />
                        <textarea id="notes" name="notes" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="4">{{ old('notes') }}</textarea>
                        <x-input-error for="notes" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('owner.room-inspections.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <x-button>
                            {{ __('Save Inspection') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
