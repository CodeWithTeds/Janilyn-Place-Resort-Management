<aside class="flex flex-col w-64 bg-brand-600 border-r border-brand-700 min-h-screen">
    <div class="flex items-center justify-center h-16 border-b border-brand-700">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
            <x-application-mark class="block h-9 w-auto text-white" />
            <span class="font-bold text-lg text-white">Janilyn's Place</span>
        </a>
    </div>

    <div class="flex flex-col flex-1 overflow-y-auto">
        <nav class="flex-1 px-4 py-4 space-y-2">
            <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                {{ __('Dashboard') }}
            </x-sidebar-link>

            @if(Auth::check() && Auth::user()->isOwner())
                <div x-data="{ open: {{ request()->routeIs('owner.inventory.*') || request()->routeIs('owner.damage-reports.*') || request()->routeIs('owner.room-inspections.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="flex items-center w-full px-4 py-2 text-brand-100 hover:bg-brand-700 hover:text-white rounded-md group focus:outline-none transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span class="flex-1 text-left font-medium">{{ __('Accommodation Management') }}</span>
                        <svg :class="{'rotate-90': open}" class="w-4 h-4 ml-auto transition-transform duration-200 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="mt-1 space-y-1 pl-4" x-cloak>
                        <x-sidebar-link href="{{ route('owner.inventory.index') }}" :active="request()->routeIs('owner.inventory.*')" class="text-sm">
                            {{ __('Inventory and amenity tracking') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ route('owner.damage-reports.index') }}" :active="request()->routeIs('owner.damage-reports.*')" class="text-sm">
                            {{ __('Damage & Incident Reporting') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ route('owner.room-inspections.index') }}" :active="request()->routeIs('owner.room-inspections.*')" class="text-sm">
                            {{ __('Room Inspection Checklists') }}
                        </x-sidebar-link>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs('owner.housekeeping.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="flex items-center w-full px-4 py-2 text-brand-100 hover:bg-brand-700 hover:text-white rounded-md group focus:outline-none transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                        <span class="flex-1 text-left font-medium">{{ __('Housekeeping') }}</span>
                        <svg :class="{'rotate-90': open}" class="w-4 h-4 ml-auto transition-transform duration-200 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="mt-1 space-y-1 pl-4" x-cloak>
                        <x-sidebar-link href="{{ route('owner.housekeeping.index') }}" :active="request()->routeIs('owner.housekeeping.index')" class="text-sm">
                            {{ __('Overview & Tasks') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ route('owner.housekeeping.schedules') }}" :active="request()->routeIs('owner.housekeeping.schedules')" class="text-sm">
                            {{ __('Room Cleaning Schedules') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ route('owner.housekeeping.staff') }}" :active="request()->routeIs('owner.housekeeping.staff')" class="text-sm">
                            {{ __('Staff Management') }}
                        </x-sidebar-link>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs('owner.resort-management.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="flex items-center w-full px-4 py-2 text-brand-100 hover:bg-brand-700 hover:text-white rounded-md group focus:outline-none transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="flex-1 text-left font-medium">{{ __('Resort Management') }}</span>
                        <svg :class="{'rotate-90': open}" class="w-4 h-4 ml-auto transition-transform duration-200 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="mt-1 space-y-1 pl-4" x-cloak>
                        <x-sidebar-link href="{{ route('owner.resort-management.bookings') }}" :active="request()->routeIs('owner.resort-management.bookings')" class="text-sm">
                            {{ __('Bookings') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ route('owner.resort-management.calendar') }}" :active="request()->routeIs('owner.resort-management.calendar')" class="text-sm">
                            {{ __('Calendar') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ route('owner.resort-management.check-in-out') }}" :active="request()->routeIs('owner.resort-management.check-in-out')" class="text-sm">
                            {{ __('Check In/Out') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ route('owner.resort-management.cancellations') }}" :active="request()->routeIs('owner.resort-management.cancellations')" class="text-sm">
                            {{ __('Cancellations') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ route('owner.resort-management.room-types.index') }}" :active="request()->routeIs('owner.resort-management.room-types.*')" class="text-sm">
                            {{ __('Room Types') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ route('owner.resort-management.resort-units.index') }}" :active="request()->routeIs('owner.resort-management.resort-units.*')" class="text-sm">
                            {{ __('Resort Units') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ route('owner.resort-management.exclusive-resort-rentals.index') }}" :active="request()->routeIs('owner.resort-management.exclusive-resort-rentals.*')" class="text-sm">
                            {{ __('Exclusive Rentals') }}
                        </x-sidebar-link>
                    </div>
                </div>
            @endif
            
            <!-- Add more sidebar links here -->
        </nav>
    </div>
</aside>
