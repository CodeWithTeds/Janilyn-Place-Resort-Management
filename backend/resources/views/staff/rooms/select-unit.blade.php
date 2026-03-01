<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Select Unit for Booking') }} #{{ $booking->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Booking Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                        <div>
                            <p class="text-sm text-gray-600">Guest Name:</p>
                            <p class="font-semibold">{{ $booking->guest_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Room Type:</p>
                            <p class="font-semibold">{{ $booking->roomType->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Check-in:</p>
                            <p class="font-semibold">{{ $booking->check_in->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Check-out:</p>
                            <p class="font-semibold">{{ $booking->check_out->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <h3 class="text-lg font-medium text-gray-900 mb-4">Available Units</h3>
                
                <form action="{{ route('staff.rooms.store', $booking) }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        @forelse ($availableUnits as $unit)
                            <div class="relative">
                                <label class="cursor-pointer">
                                    <input type="radio" name="resort_unit_id" value="{{ $unit->id }}" class="peer sr-only" {{ $booking->resort_unit_id == $unit->id ? 'checked' : '' }} required>
                                    <div class="p-4 bg-white border rounded-lg hover:bg-gray-50 peer-checked:ring-2 peer-checked:ring-indigo-500 peer-checked:border-transparent">
                                        <div class="font-semibold">{{ $unit->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $unit->cleaning_status->label() }}</div>
                                    </div>
                                </label>
                            </div>
                        @empty
                            <div class="col-span-3 text-center text-red-500 font-semibold">
                                No units available for this room type on the selected dates.
                            </div>
                        @endforelse
                    </div>

                    @if($availableUnits->isNotEmpty())
                        <div class="flex items-center justify-end">
                            <a href="{{ route('staff.rooms.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Confirm Allocation
                            </button>
                        </div>
                    @else
                        <div class="flex items-center justify-end">
                            <a href="{{ route('staff.rooms.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Back to List
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
