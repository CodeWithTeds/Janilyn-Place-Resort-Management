<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100 flex">
            <!-- Sidebar -->
            <x-sidebar />

            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
                @livewire('navigation-menu')

                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                    @if(Auth::check() && Auth::user()->isStaff() && !request()->routeIs('staff.dashboard'))
                        @php
                            $staffAssignedTasks = \App\Models\StaffTask::with('createdBy')
                                ->where('assigned_to', Auth::id())
                                ->orderByRaw("CASE WHEN status = 'in_progress' THEN 1 WHEN status = 'pending' THEN 2 ELSE 3 END")
                                ->orderBy('due_date')
                                ->limit(3)
                                ->get();
                            $housekeepingAssignedTasks = \App\Models\HousekeepingTask::with('resortUnit')
                                ->where('assigned_to', Auth::id())
                                ->orderByRaw("CASE WHEN status = 'in_progress' THEN 1 WHEN status = 'pending' THEN 2 ELSE 3 END")
                                ->orderBy('due_date')
                                ->limit(3)
                                ->get();
                        @endphp
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                                    <h3 class="text-sm font-semibold text-gray-900">My Assigned Work</h3>
                                    <a href="{{ route('staff.dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View all</a>
                                </div>
                                <div class="p-4">
                                    @if($staffAssignedTasks->isEmpty() && $housekeepingAssignedTasks->isEmpty())
                                        <p class="text-sm text-gray-500">No assigned work yet.</p>
                                    @else
                                        @if($staffAssignedTasks->isNotEmpty())
                                            <h4 class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Staff Management</h4>
                                            <div class="space-y-2 mb-4">
                                                @foreach($staffAssignedTasks as $task)
                                                    <div class="flex items-center justify-between text-sm">
                                                        <div class="text-gray-800 truncate pr-4">{{ $task->title }}</div>
                                                        <div class="text-gray-500 whitespace-nowrap">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if($housekeepingAssignedTasks->isNotEmpty())
                                            <h4 class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Housekeeping</h4>
                                            <div class="space-y-2">
                                                @foreach($housekeepingAssignedTasks as $task)
                                                    <div class="flex items-center justify-between text-sm">
                                                        <div class="text-gray-800 truncate pr-4">{{ $task->title }}</div>
                                                        <div class="text-gray-500 whitespace-nowrap">{{ $task->status->label() }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('modals')

        @livewireScripts
        @stack('scripts')

        {{-- Flash Messages Data Container --}}
        <div id="flash-messages" 
             data-success="{{ session('success') }}" 
             data-error="{{ session('error') }}" 
             data-warning="{{ session('warning') }}" 
             data-info="{{ session('info') }}" 
             style="display: none;">
        </div>
    </body>
</html>
