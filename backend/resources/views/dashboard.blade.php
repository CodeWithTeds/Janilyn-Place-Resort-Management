<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900">Welcome to Janilyn's Place Dashboard</h3>
                <p class="mt-1 text-sm text-gray-600">This is your new dashboard layout. You can start adding your dynamic content here.</p>
            </div>
        </div>
    </div>
</x-app-layout>
