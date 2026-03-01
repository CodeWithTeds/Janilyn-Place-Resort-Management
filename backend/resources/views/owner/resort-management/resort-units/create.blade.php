<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Resort Unit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6" 
                 x-data="{ 
                    roomTypeId: '{{ old('room_type_id') }}', 
                    tiers: [],
                    async fetchTiers() {
                        if (!this.roomTypeId) {
                            this.tiers = [];
                            return;
                        }
                        try {
                            const response = await fetch(`/api/room-types/${this.roomTypeId}`);
                            const data = await response.json();
                            // Filter only global tiers (resort_unit_id is null)
                            // But for pre-filling, we want to show the base tiers of the room type.
                            // The user can then edit them, which will create NEW tiers specific to this unit.
                            // We map them to remove 'id' so they are treated as new entries.
                            this.tiers = data.pricing_tiers
                                .filter(t => t.resort_unit_id === null)
                                .map(t => ({
                                    min_guests: t.min_guests,
                                    max_guests: t.max_guests,
                                    price_weekday: t.price_weekday,
                                    price_weekend: t.price_weekend
                                }));
                        } catch (error) {
                            console.error('Error fetching tiers:', error);
                        }
                    }
                 }"
                 x-init="$watch('roomTypeId', () => fetchTiers())">
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
                            <select id="room_type_id" name="room_type_id" x-model="roomTypeId" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
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

                        <!-- Unit-Specific Pricing Tiers -->
                        <div class="col-span-1 md:col-span-2 mt-6" x-show="tiers.length > 0 || roomTypeId">
                            <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Unit-Specific Pricing Tiers (Optional)
                                </h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    These tiers are pre-filled from the selected Room Type. 
                                    Modify them here to create specific pricing for this unit.
                                </p>

                                <div class="space-y-4">
                                    <template x-for="(tier, index) in tiers" :key="index">
                                        <div class="border rounded-lg bg-white overflow-hidden shadow-sm" x-data="{ expanded: false }">
                                            <!-- Tier Header / Summary -->
                                            <div @click="expanded = !expanded" class="p-4 flex justify-between items-center cursor-pointer hover:bg-gray-50 transition-colors">
                                                <div class="flex items-center space-x-4">
                                                    <span class="font-medium text-gray-700">Tier <span x-text="index + 1"></span></span>
                                                    <span class="text-sm text-gray-500" x-show="tier.min_guests && tier.max_guests">
                                                        (<span x-text="tier.min_guests"></span> - <span x-text="tier.max_guests"></span> Guests)
                                                    </span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <button type="button" @click.stop="tiers.splice(index, 1)" class="text-red-500 hover:text-red-700 p-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                    <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </div>
                                            </div>

                                            <!-- Expanded Details -->
                                            <div x-show="expanded" x-collapse class="p-4 border-t border-gray-200 bg-gray-50 grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block font-medium text-xs text-gray-700">Min Guests</label>
                                                    <input type="number" :name="`pricing_tiers[${index}][min_guests]`" x-model="tier.min_guests" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required placeholder="1">
                                                </div>
                                                <div>
                                                    <label class="block font-medium text-xs text-gray-700">Max Guests</label>
                                                    <input type="number" :name="`pricing_tiers[${index}][max_guests]`" x-model="tier.max_guests" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required placeholder="2">
                                                </div>
                                                <div>
                                                    <label class="block font-medium text-xs text-gray-700">Weekday ₱</label>
                                                    <input type="number" step="0.01" :name="`pricing_tiers[${index}][price_weekday]`" x-model="tier.price_weekday" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required placeholder="0.00">
                                                </div>
                                                <div>
                                                    <label class="block font-medium text-xs text-gray-700">Weekend ₱</label>
                                                    <input type="number" step="0.01" :name="`pricing_tiers[${index}][price_weekend]`" x-model="tier.price_weekend" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required placeholder="0.00">
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <button type="button" @click="tiers.push({ min_guests: '', max_guests: '', price_weekday: '', price_weekend: '' })" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        + Add Unit Tier
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('resort-management.resort-units.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <x-button class="ml-4">
                            {{ __('Create Unit') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
