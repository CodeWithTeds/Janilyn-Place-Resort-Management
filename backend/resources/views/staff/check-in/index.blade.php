<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Check-in / Check-out') }} - {{ now()->format('M d, Y') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Today's Check-ins</h3>
                @if($checkIns->isEmpty())
                    <p class="text-gray-500">No check-ins scheduled for today.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($checkIns as $booking)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->guest_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->resortUnit->name ?? 'Unassigned' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                {{ $booking->status->label() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($booking->resort_unit_id)
                                                <form action="{{ route('staff.check-in.store', $booking) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900 font-bold">Check In</button>
                                                </form>
                                            @else
                                                <span class="text-red-500 text-sm">Assign Room First</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Today's Check-outs</h3>
                @if($checkOuts->isEmpty())
                    <p class="text-gray-500">No check-outs scheduled for today.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($checkOuts as $booking)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->guest_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->resortUnit->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $booking->status->label() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <form action="{{ route('staff.check-out.store', $booking) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-bold">Check Out</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
