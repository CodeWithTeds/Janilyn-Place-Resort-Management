<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Room Availability and Calendar View') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                @php
                    $prevMonth = $currentDate->copy()->subMonth();
                    $nextMonth = $currentDate->copy()->addMonth();
                    $startDayOfWeek = $currentDate->copy()->startOfMonth()->dayOfWeek;
                    $daysInMonth = $currentDate->daysInMonth;
                    $today = \Carbon\Carbon::today();
                @endphp

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ $currentDate->format('F Y') }}</h3>
                    <div class="flex space-x-2">
                        <a href="{{ route('resort-management.calendar', ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}" class="px-3 py-1 border rounded hover:bg-gray-100 text-gray-700">&lt; Prev</a>
                        <a href="{{ route('resort-management.calendar', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}" class="px-3 py-1 border rounded hover:bg-gray-100 text-gray-700">Next &gt;</a>
                    </div>
                </div>

                <div class="grid grid-cols-7 gap-px bg-gray-200 border border-gray-200">
                    <div class="bg-gray-50 p-2 text-center text-xs font-semibold uppercase text-gray-500">Sun</div>
                    <div class="bg-gray-50 p-2 text-center text-xs font-semibold uppercase text-gray-500">Mon</div>
                    <div class="bg-gray-50 p-2 text-center text-xs font-semibold uppercase text-gray-500">Tue</div>
                    <div class="bg-gray-50 p-2 text-center text-xs font-semibold uppercase text-gray-500">Wed</div>
                    <div class="bg-gray-50 p-2 text-center text-xs font-semibold uppercase text-gray-500">Thu</div>
                    <div class="bg-gray-50 p-2 text-center text-xs font-semibold uppercase text-gray-500">Fri</div>
                    <div class="bg-gray-50 p-2 text-center text-xs font-semibold uppercase text-gray-500">Sat</div>

                    <!-- Empty cells for start of month -->
                    @for ($i = 0; $i < $startDayOfWeek; $i++)
                        <div class="bg-gray-50 h-32 p-2 border-t border-gray-200"></div>
                    @endfor

                    <!-- Days -->
                    @for ($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $date = $currentDate->copy()->day($day);
                            $dayBookings = $bookings->filter(function($booking) use ($date) {
                                $checkIn = \Carbon\Carbon::parse($booking->check_in)->startOfDay();
                                $checkOut = \Carbon\Carbon::parse($booking->check_out)->startOfDay();
                                // Show booking if current date is >= checkIn AND < checkOut
                                return $date->gte($checkIn) && $date->lt($checkOut);
                            });
                        @endphp
                        <div class="bg-white h-32 p-2 border-t border-gray-200 relative hover:bg-gray-50 overflow-y-auto group">
                            <span class="text-sm font-semibold {{ $date->isToday() ? 'bg-indigo-600 text-white rounded-full w-6 h-6 flex items-center justify-center' : 'text-gray-700' }}">{{ $day }}</span>
                            
                            <div class="mt-1 space-y-1">
                                @foreach($dayBookings as $booking)
                                    @php
                                        $statusClass = match($booking->status) {
                                            \App\Enums\BookingStatus::CONFIRMED => 'bg-blue-100 text-blue-800',
                                            \App\Enums\BookingStatus::PENDING => 'bg-yellow-100 text-yellow-800',
                                            \App\Enums\BookingStatus::COMPLETED => 'bg-gray-100 text-gray-800',
                                            \App\Enums\BookingStatus::CANCELLED => 'bg-red-100 text-red-800 line-through',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                        
                                        // Highlight check-in/out days visually if needed
                                        $isCheckIn = \Carbon\Carbon::parse($booking->check_in)->isSameDay($date);
                                    @endphp
                                    <div class="text-xs px-2 py-1 rounded truncate {{ $statusClass }}" 
                                         title="{{ $booking->guest_name }} ({{ $booking->roomType ? $booking->roomType->name : ($booking->exclusiveResortRental ? $booking->exclusiveResortRental->name : 'N/A') }})">
                                        @if($isCheckIn) <span class="font-bold">↳</span> @endif
                                        @if($booking->roomType)
                                            {{ $booking->roomType->name }}
                                        @elseif($booking->exclusiveResortRental)
                                            <span class="text-purple-800 font-bold">★ {{ $booking->exclusiveResortRental->name }}</span>
                                        @else
                                            Unknown Room
                                        @endif
                                        <span class="hidden sm:inline opacity-75">- {{ $booking->guest_name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endfor
                    
                    <!-- Empty cells for end of month to complete the grid -->
                    @php
                        $remainingCells = (7 - (($startDayOfWeek + $daysInMonth) % 7)) % 7;
                    @endphp
                    @for ($i = 0; $i < $remainingCells; $i++)
                        <div class="bg-gray-50 h-32 p-2 border-t border-gray-200"></div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
