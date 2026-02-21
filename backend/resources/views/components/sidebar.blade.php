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
                    </div>
                </div>
            @endif
            
            <!-- Add more sidebar links here -->
        </nav>
    </div>
</aside>
