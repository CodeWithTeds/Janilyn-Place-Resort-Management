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
            
            <!-- Add more sidebar links here -->
        </nav>
    </div>
</aside>
