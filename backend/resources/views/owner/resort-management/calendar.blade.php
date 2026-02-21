<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Room Availability and Calendar View') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">February 2026</h3>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 border rounded hover:bg-gray-100">&lt; Prev</button>
                        <button class="px-3 py-1 border rounded hover:bg-gray-100">Next &gt;</button>
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

                    <!-- Days -->
                    @for ($i = 1; $i <= 28; $i++)
                        <div class="bg-white h-32 p-2 border-t border-gray-200 relative hover:bg-gray-50">
                            <span class="text-sm font-semibold text-gray-700">{{ $i }}</span>
                            @if($i == 5 || $i == 12 || $i == 20)
                                <div class="mt-1 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded truncate">
                                    Deluxe Room (Occupied)
                                </div>
                            @endif
                            @if($i == 8 || $i == 15)
                                <div class="mt-1 bg-green-100 text-green-800 text-xs px-2 py-1 rounded truncate">
                                    Suite (Reserved)
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
