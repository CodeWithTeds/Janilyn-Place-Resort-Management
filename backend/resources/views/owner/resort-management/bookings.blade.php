<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reservation and Booking') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ tab: 'walk-in' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Tabs -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="tab = 'walk-in'" :class="{ 'border-brand-500 text-brand-600': tab === 'walk-in', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'walk-in' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Walk-in Booking
                    </button>
                    <button @click="tab = 'online'" :class="{ 'border-brand-500 text-brand-600': tab === 'online', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'online' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Online Reservations
                    </button>
                </nav>
            </div>

            <!-- Walk-in Booking Form -->
            <div x-show="tab === 'walk-in'" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">New Walk-in Reservation</h3>
                <form>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-label for="guest_name" value="{{ __('Guest Name') }}" />
                            <x-input id="guest_name" class="block mt-1 w-full" type="text" name="guest_name" required />
                        </div>
                        <div>
                            <x-label for="room_type" value="{{ __('Room Type') }}" />
                            <select id="room_type" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option>Standard Room</option>
                                <option>Deluxe Room</option>
                                <option>Suite</option>
                            </select>
                        </div>
                        <div>
                            <x-label for="check_in" value="{{ __('Check-in Date') }}" />
                            <x-input id="check_in" class="block mt-1 w-full" type="date" name="check_in" required />
                        </div>
                        <div>
                            <x-label for="check_out" value="{{ __('Check-out Date') }}" />
                            <x-input id="check_out" class="block mt-1 w-full" type="date" name="check_out" required />
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <x-button>
                            {{ __('Create Booking') }}
                        </x-button>
                    </div>
                </form>
            </div>

            <!-- Online Reservations List -->
            <div x-show="tab === 'online'" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6" x-cloak>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pending Online Reservations</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">John Doe</td>
                                <td class="px-6 py-4 whitespace-nowrap">Deluxe Room</td>
                                <td class="px-6 py-4 whitespace-nowrap">Feb 25 - Feb 28</td>
                                <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="text-indigo-600 hover:text-indigo-900 mr-3">Approve</button>
                                    <button class="text-red-600 hover:text-red-900">Reject</button>
                                </td>
                            </tr>
                            <!-- More rows... -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
