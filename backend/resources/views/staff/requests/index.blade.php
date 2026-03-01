<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Special Requests') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Guest Requests</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($bookings as $booking)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 shadow-sm">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-lg text-gray-800">{{ $booking->guest_name }}</h4>
                                <span class="text-xs font-semibold px-2 py-1 bg-white border rounded text-gray-500">
                                    {{ $booking->resortUnit->name ?? 'No Room' }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-600 mb-4">
                                <p>Check-in: {{ $booking->check_in->format('M d') }}</p>
                                <p>Check-out: {{ $booking->check_out->format('M d') }}</p>
                            </div>
                            
                            <div class="bg-white p-3 rounded border border-gray-100 text-sm text-gray-700 mb-4 h-32 overflow-y-auto whitespace-pre-wrap">{{ $booking->notes }}</div>

                            <form action="{{ route('staff.requests.store', $booking) }}" method="POST" class="mt-2">
                                @csrf
                                <div class="flex">
                                    <input type="text" name="request" placeholder="Add new request/note..." class="flex-1 text-sm border-gray-300 rounded-l-md focus:ring-indigo-500 focus:border-indigo-500" required>
                                    <button type="submit" class="bg-indigo-600 text-white px-3 py-2 rounded-r-md text-sm hover:bg-indigo-700">Add</button>
                                </div>
                            </form>
                        </div>
                    @empty
                        <div class="col-span-3 text-center text-gray-500 py-8">
                            No active bookings with special requests found.
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
