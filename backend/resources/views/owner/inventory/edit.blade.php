<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Inventory Item') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('owner.inventory.update', $inventory) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-label for="name" value="{{ __('Item Name') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $inventory->name)" required autofocus />
                            <x-input-error for="name" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="category" value="{{ __('Category') }}" />
                            <select id="category" name="category" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="Linen" {{ old('category', $inventory->category) == 'Linen' ? 'selected' : '' }}>Linen (Sheets, Towels, etc.)</option>
                                <option value="Toiletries" {{ old('category', $inventory->category) == 'Toiletries' ? 'selected' : '' }}>Toiletries (Soap, Shampoo, etc.)</option>
                                <option value="Cleaning Supplies" {{ old('category', $inventory->category) == 'Cleaning Supplies' ? 'selected' : '' }}>Cleaning Supplies</option>
                                <option value="Minibar" {{ old('category', $inventory->category) == 'Minibar' ? 'selected' : '' }}>Minibar Items</option>
                                <option value="Equipment" {{ old('category', $inventory->category) == 'Equipment' ? 'selected' : '' }}>Equipment</option>
                                <option value="Others" {{ old('category', $inventory->category) == 'Others' ? 'selected' : '' }}>Others</option>
                            </select>
                            <x-input-error for="category" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="quantity" value="{{ __('Current Quantity') }}" />
                            <x-input id="quantity" class="block mt-1 w-full" type="number" name="quantity" :value="old('quantity', $inventory->quantity)" required min="0" />
                            <x-input-error for="quantity" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="unit" value="{{ __('Unit of Measurement') }}" />
                            <x-input id="unit" class="block mt-1 w-full" type="text" name="unit" :value="old('unit', $inventory->unit)" required placeholder="pcs, box, set, etc." />
                            <x-input-error for="unit" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="reorder_level" value="{{ __('Low Stock Alert Level') }}" />
                            <x-input id="reorder_level" class="block mt-1 w-full" type="number" name="reorder_level" :value="old('reorder_level', $inventory->reorder_level)" required min="0" />
                            <p class="text-xs text-gray-500 mt-1">Alert when stock falls below this number.</p>
                            <x-input-error for="reorder_level" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="location" value="{{ __('Storage Location') }}" />
                            <x-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location', $inventory->location)" placeholder="e.g. Storage Room A" />
                            <x-input-error for="location" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-label for="cost_per_unit" value="{{ __('Cost Per Unit (Optional)') }}" />
                            <x-input id="cost_per_unit" class="block mt-1 w-full" type="number" step="0.01" name="cost_per_unit" :value="old('cost_per_unit', $inventory->cost_per_unit)" min="0" />
                            <x-input-error for="cost_per_unit" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-label for="description" value="{{ __('Description (Optional)') }}" />
                        <textarea id="description" name="description" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="3">{{ old('description', $inventory->description) }}</textarea>
                        <x-input-error for="description" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('owner.inventory.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <x-button>
                            {{ __('Update Item') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
