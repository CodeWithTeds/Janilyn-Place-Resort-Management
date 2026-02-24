<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Report New Damage / Incident') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('owner.damage-reports.store') }}" method="POST" enctype="multipart/form-data">
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
                            <x-label for="item_name" value="{{ __('Damaged Item / Issue') }}" />
                            <x-input id="item_name" class="block mt-1 w-full" type="text" name="item_name" :value="old('item_name')" required placeholder="e.g. Bed Frame, AC Unit" />
                            <x-input-error for="item_name" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="severity" value="{{ __('Severity') }}" />
                            <select id="severity" name="severity" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="Low" {{ old('severity') == 'Low' ? 'selected' : '' }}>Low (Cosmetic, Minor)</option>
                                <option value="Medium" {{ old('severity') == 'Medium' ? 'selected' : '' }}>Medium (Needs Repair)</option>
                                <option value="High" {{ old('severity') == 'High' ? 'selected' : '' }}>High (Urgent Repair)</option>
                                <option value="Critical" {{ old('severity') == 'Critical' ? 'selected' : '' }}>Critical (Safety Hazard)</option>
                            </select>
                            <x-input-error for="severity" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="status" value="{{ __('Status') }}" />
                            <select id="status" name="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="Reported" {{ old('status') == 'Reported' ? 'selected' : '' }}>Reported</option>
                                <option value="In Progress" {{ old('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Resolved" {{ old('status') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                            <x-input-error for="status" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="cost_estimate" value="{{ __('Estimated Cost to Fix (Optional)') }}" />
                            <x-input id="cost_estimate" class="block mt-1 w-full" type="number" step="0.01" name="cost_estimate" :value="old('cost_estimate')" min="0" />
                            <x-input-error for="cost_estimate" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-label for="description" value="{{ __('Description of Damage') }}" />
                        <textarea id="description" name="description" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="4" required>{{ old('description') }}</textarea>
                        <x-input-error for="description" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('owner.damage-reports.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <x-button>
                            {{ __('Submit Report') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
