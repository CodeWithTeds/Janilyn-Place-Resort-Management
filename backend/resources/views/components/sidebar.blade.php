<aside class="flex flex-col w-64 bg-brand-600 border-r border-brand-700 min-h-screen">
    <div class="flex items-center justify-center h-16 border-b border-brand-700">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
            <x-application-mark class="block h-9 w-auto text-white" />
            <span class="font-bold text-lg text-white">Janilyn's Place</span>
        </a>
    </div>

    <div class="flex flex-col flex-1 overflow-y-auto">
        <nav class="flex-1 px-4 py-4 space-y-2">
            @php($isAdmin = Auth::check() && Auth::user()->isAdmin())

            @if(Auth::check() && Auth::user()->isStaff())
                <x-sidebar-link href="{{ route('staff.dashboard') }}" :active="request()->routeIs('staff.dashboard')">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-8 7h8m-8 4h5"></path>
                    </svg>
                    {{ __('My Assigned Work') }}
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('staff.damage-reports.index') }}" :active="request()->routeIs('staff.damage-reports.*')">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 00-2-2H5m0 0l-2 2m2-2l7-7 7 7M5 9v10a2 2 0 002 2h10a2 2 0 002-2V9"></path>
                    </svg>
                    {{ __('Damage & Incident Reporting') }}
                </x-sidebar-link>
            @endif

            @if(Auth::check() && (Auth::user()->isOwner() || Auth::user()->isAdmin()))
                <x-sidebar-link href="{{ $isAdmin ? route('admin.analytics.index') : route('owner.analytics.index') }}" :active="request()->routeIs('owner.analytics.index') || request()->routeIs('admin.analytics.index')">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    {{ __('Analytics & Reporting') }}
                </x-sidebar-link>

                <div x-data="{ open: {{ request()->routeIs('owner.inventory.*') || request()->routeIs('owner.damage-reports.*') || request()->routeIs('owner.room-inspections.*') || request()->routeIs('admin.inventory.*') || request()->routeIs('admin.damage-reports.*') || request()->routeIs('admin.room-inspections.*') ? 'true' : 'false' }} }">
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
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.inventory.index') : route('owner.inventory.index') }}" :active="request()->routeIs('owner.inventory.*') || request()->routeIs('admin.inventory.*')" class="text-sm">
                            {{ __('Inventory and amenity tracking') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.damage-reports.index') : route('owner.damage-reports.index') }}" :active="request()->routeIs('owner.damage-reports.*') || request()->routeIs('admin.damage-reports.*')" class="text-sm">
                            {{ __('Damage & Incident Reporting') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.room-inspections.index') : route('owner.room-inspections.index') }}" :active="request()->routeIs('owner.room-inspections.*') || request()->routeIs('admin.room-inspections.*')" class="text-sm">
                            {{ __('Room Inspection Checklists') }}
                        </x-sidebar-link>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs('owner.housekeeping.*') || request()->routeIs('admin.housekeeping.*') ? 'true' : 'false' }} }">
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
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.housekeeping.index') : route('owner.housekeeping.index') }}" :active="request()->routeIs('owner.housekeeping.index') || request()->routeIs('admin.housekeeping.index')" class="text-sm">
                            {{ __('Overview & Tasks') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.housekeeping.schedules') : route('owner.housekeeping.schedules') }}" :active="request()->routeIs('owner.housekeeping.schedules') || request()->routeIs('admin.housekeeping.schedules')" class="text-sm">
                            {{ __('Room Cleaning Schedules') }}
                        </x-sidebar-link>
                    
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs('owner.staff-management.*') || request()->routeIs('admin.staff-management.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="flex items-center w-full px-4 py-2 text-brand-100 hover:bg-brand-700 hover:text-white rounded-md group focus:outline-none transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span class="flex-1 text-left font-medium">{{ __('Staff Management') }}</span>
                        <svg :class="{'rotate-90': open}" class="w-4 h-4 ml-auto transition-transform duration-200 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="mt-1 space-y-1 pl-4" x-cloak>
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.staff-management.index') : route('owner.staff-management.index') }}" :active="request()->routeIs('owner.staff-management.index') || request()->routeIs('admin.staff-management.index')" class="text-sm">
                            {{ __('Staff List') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.staff-management.schedules') : route('owner.staff-management.schedules') }}" :active="request()->routeIs('owner.staff-management.schedules') || request()->routeIs('admin.staff-management.schedules')" class="text-sm">
                            {{ __('Schedules') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.staff-management.tasks') : route('owner.staff-management.tasks') }}" :active="request()->routeIs('owner.staff-management.tasks') || request()->routeIs('admin.staff-management.tasks')" class="text-sm">
                            {{ __('Tasks') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.staff-management.attendance') : route('owner.staff-management.attendance') }}" :active="request()->routeIs('owner.staff-management.attendance') || request()->routeIs('admin.staff-management.attendance')" class="text-sm">
                            {{ __('Attendance') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.staff-management.performance') : route('owner.staff-management.performance') }}" :active="request()->routeIs('owner.staff-management.performance') || request()->routeIs('admin.staff-management.performance')" class="text-sm">
                            {{ __('Performance') }}
                        </x-sidebar-link>
                    </div>
                </div>

            @endif

            @if(Auth::check() && (Auth::user()->isOwner() || Auth::user()->isAdmin() || Auth::user()->isStaff()))
                <div x-data="{ open: {{ request()->routeIs('resort-management.*') || request()->routeIs('admin.resort-management.*') ? 'true' : 'false' }} }">
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
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.resort-management.bookings') : route('resort-management.bookings') }}" :active="request()->routeIs('resort-management.bookings') || request()->routeIs('admin.resort-management.bookings')" class="text-sm">
                            {{ __('Bookings') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.resort-management.calendar') : route('resort-management.calendar') }}" :active="request()->routeIs('resort-management.calendar') || request()->routeIs('admin.resort-management.calendar')" class="text-sm">
                            {{ __('Calendar') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.resort-management.check-in-out') : route('resort-management.check-in-out') }}" :active="request()->routeIs('resort-management.check-in-out') || request()->routeIs('admin.resort-management.check-in-out')" class="text-sm">
                            {{ __('Check In/Out') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.resort-management.cancellations') : route('resort-management.cancellations') }}" :active="request()->routeIs('resort-management.cancellations') || request()->routeIs('admin.resort-management.cancellations')" class="text-sm">
                            {{ __('Cancellations') }}
                        </x-sidebar-link>
                        @if(Auth::check() && (Auth::user()->isOwner() || Auth::user()->isAdmin()))
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.resort-management.feedback') : route('resort-management.feedback') }}" :active="request()->routeIs('resort-management.feedback') || request()->routeIs('admin.resort-management.feedback')" class="text-sm">
                            {{ __('Feedback') }}
                        </x-sidebar-link>
                        @endif
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.resort-management.room-types.index') : route('resort-management.room-types.index') }}" :active="request()->routeIs('resort-management.room-types.*') || request()->routeIs('admin.resort-management.room-types.*')" class="text-sm">
                            {{ __('Room Types') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.resort-management.resort-units.index') : route('resort-management.resort-units.index') }}" :active="request()->routeIs('resort-management.resort-units.*') || request()->routeIs('admin.resort-management.resort-units.*')" class="text-sm">
                            {{ __('Resort Units') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.resort-management.exclusive-resort-rentals.index') : route('resort-management.exclusive-resort-rentals.index') }}" :active="request()->routeIs('resort-management.exclusive-resort-rentals.*') || request()->routeIs('admin.resort-management.exclusive-resort-rentals.*')" class="text-sm">
                            {{ __('Exclusive Rentals') }}
                        </x-sidebar-link>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs('resort-management.guest-management.*') || request()->routeIs('admin.resort-management.guest-management.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="flex items-center w-full px-4 py-2 text-brand-100 hover:bg-brand-700 hover:text-white rounded-md group focus:outline-none transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="flex-1 text-left font-medium">{{ __('Guest Relationship (GRM)') }}</span>
                        <svg :class="{'rotate-90': open}" class="w-4 h-4 ml-auto transition-transform duration-200 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="mt-1 space-y-1 pl-4" x-cloak>
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.resort-management.guest-management.index') : route('resort-management.guest-management.index') }}" :active="request()->routeIs('resort-management.guest-management.index') || request()->routeIs('admin.resort-management.guest-management.index')" class="text-sm">
                            {{ __('Guest History') }}
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ $isAdmin ? route('admin.resort-management.guest-management.loyalty') : route('resort-management.guest-management.loyalty') }}" :active="request()->routeIs('resort-management.guest-management.loyalty') || request()->routeIs('admin.resort-management.guest-management.loyalty')" class="text-sm">
                            {{ __('Loyalty & Rewards') }}
                        </x-sidebar-link>
                    </div>
                </div>
            @endif
            
            <!-- Add more sidebar links here -->
        </nav>
    </div>
</aside>
