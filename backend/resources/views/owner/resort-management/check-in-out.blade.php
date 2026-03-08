<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Guest Check-in and Check-out') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Check-ins Today -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Check-ins Today</h3>
                @if($checkIns->isEmpty())
                    <p class="text-gray-500">No check-ins scheduled for today.</p>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($checkIns as $booking)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->guest_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->guest_email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($booking->roomType)
                                        {{ $booking->roomType->name }}
                                    @elseif($booking->exclusiveResortRental)
                                        <span class="text-purple-600 font-semibold">{{ $booking->exclusiveResortRental->name }}</span>
                                        <span class="text-xs text-gray-400">(Exclusive)</span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($booking->status->value) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form action="{{ route('resort-management.bookings.check-in', $booking) }}" method="POST" class="inline-block check-in-form">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs">Check In</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            <!-- Check-outs Today -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Check-outs Today</h3>
                @if($checkOuts->isEmpty())
                    <p class="text-gray-500">No check-outs scheduled for today.</p>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($checkOuts as $booking)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->guest_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->guest_email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($booking->roomType)
                                        {{ $booking->roomType->name }}
                                    @elseif($booking->exclusiveResortRental)
                                        <span class="text-purple-600 font-semibold">{{ $booking->exclusiveResortRental->name }}</span>
                                        <span class="text-xs text-gray-400">(Exclusive)</span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $booking->status === \App\Enums\BookingStatus::CHECKED_IN ? 'bg-indigo-100 text-indigo-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($booking->status->value) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form 
                                        action="{{ route('resort-management.bookings.check-out', $booking) }}" 
                                        method="POST" 
                                        class="inline-block check-out-form"
                                        data-resort-unit-id="{{ $booking->resort_unit_id ?? '' }}"
                                        data-booking-id="{{ $booking->id }}"
                                        @if($booking->resort_unit_id)
                                            data-unit-status-url="{{ route('owner.housekeeping.units.status', $booking->resort_unit_id) }}"
                                        @endif
                                    >
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                            Check Out
                                        </button>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function() {
            function attachCheckoutHandlers() {
                const forms = document.querySelectorAll('.check-out-form');
                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                const csrf = tokenMeta ? tokenMeta.getAttribute('content') : null;

                forms.forEach(form => {
                    if (form.dataset.bound === '1') return;
                    form.dataset.bound = '1';
                    form.addEventListener('submit', async function (e) {
                        e.preventDefault();
                        const unitId = form.dataset.resortUnitId || '';
                        const bookingId = form.dataset.bookingId || '';

                        // Always perform the check-out first
                        try {
                            const action = form.getAttribute('action');
                            const fd = new FormData(form);
                            if (!fd.has('_method')) fd.append('_method', 'PATCH');
                            const response = await fetch(action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrf || '',
                                    'Accept': 'application/json'
                                },
                                body: fd
                            });

                            if (!response.ok) {
                                // Fallback: hard submit if JSON flow fails (e.g., session redirect)
                                form.submit();
                                return;
                            }
                        } catch (err) {
                            // Fallback to regular submission on network/script error
                            form.submit();
                            return;
                        }

                        // Optional incident report (Skip should NOT cancel the checkout)
                        if (window.Swal) {
                            const result = await Swal.fire({
                                icon: 'info',
                                title: 'Create Incident Report?',
                                text: 'Guest has been checked out and unit marked dirty. Do you want to create an incident report?',
                                showCancelButton: true,
                                confirmButtonText: 'Create Incident Report',
                                cancelButtonText: 'Skip'
                            });
                            if (result.isConfirmed) {
                                const url = new URL("{{ route('owner.damage-reports.create') }}", window.location.origin);
                                if (unitId) url.searchParams.set('resort_unit_id', unitId);
                                if (bookingId) url.searchParams.set('booking_id', bookingId);
                                url.searchParams.set('from_checkout', '1');
                                window.location.href = url.toString();
                                return;
                            }
                            // Reload to reflect checkout status if user skips
                            window.location.reload();
                            return;
                        }

                        // No SweetAlert available: simple confirm
                        if (confirm('Guest checked out. Create an incident report? Click OK to create, Cancel to skip.')) {
                            const url = new URL("{{ route('owner.damage-reports.create') }}", window.location.origin);
                            if (unitId) url.searchParams.set('resort_unit_id', unitId);
                            if (bookingId) url.searchParams.set('booking_id', bookingId);
                            url.searchParams.set('from_checkout', '1');
                            window.location.href = url.toString();
                        } else {
                            window.location.reload();
                        }
                    });
                });
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', attachCheckoutHandlers);
            } else {
                attachCheckoutHandlers();
            }
        })();
    </script>
</x-app-layout>
