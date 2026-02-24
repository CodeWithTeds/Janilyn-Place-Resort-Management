<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Housekeeping Management') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('owner.housekeeping.staff') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Manage Staff
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('owner.housekeeping.index') }}" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <x-label for="status" value="{{ __('Status') }}" />
                        <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Statuses</option>
                            @foreach(\App\Enums\HousekeepingStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-label for="priority" value="{{ __('Priority') }}" />
                        <select id="priority" name="priority" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Priorities</option>
                            @foreach(\App\Enums\HousekeepingPriority::cases() as $priority)
                                <option value="{{ $priority->value }}" {{ request('priority') == $priority->value ? 'selected' : '' }}>
                                    {{ $priority->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-label for="assigned_to" value="{{ __('Assigned To') }}" />
                        <select id="assigned_to" name="assigned_to" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Staff</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}" {{ request('assigned_to') == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex space-x-2">
                        <x-button type="submit">Filter</x-button>
                        @if(request()->hasAny(['status', 'priority', 'assigned_to']))
                            <a href="{{ route('owner.housekeeping.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tasks Table -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tasks as $task)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($task->description, 30) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $task->resortUnit->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($task->assignee)
                                            <div class="text-sm text-gray-900">{{ $task->assignee->name }}</div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ match($task->priority->value) {
                                                'low' => 'bg-gray-100 text-gray-800',
                                                'medium' => 'bg-blue-100 text-blue-800',
                                                'high' => 'bg-orange-100 text-orange-800',
                                                'urgent' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            } }}">
                                            {{ $task->priority->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ match($task->status->value) {
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'in_progress' => 'bg-blue-100 text-blue-800',
                                                'completed' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-gray-100 text-gray-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            } }}">
                                            {{ $task->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $task->due_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <form action="{{ route('owner.housekeeping.tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                        No tasks found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $tasks->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <x-modal name="add-task-modal" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Create New Housekeeping Task') }}
            </h2>

            <form method="POST" action="{{ route('owner.housekeeping.tasks.store') }}" class="mt-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <x-label for="title" value="{{ __('Task Title') }}" />
                        <x-input id="title" class="block mt-1 w-full" type="text" name="title" required />
                    </div>

                    <div class="col-span-2">
                        <x-label for="description" value="{{ __('Description') }}" />
                        <textarea id="description" name="description" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="3"></textarea>
                    </div>

                    <div>
                        <x-label for="resort_unit_id" value="{{ __('Unit') }}" />
                        <select id="resort_unit_id" name="resort_unit_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">Select Unit</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->status }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-label for="assigned_to" value="{{ __('Assign To (Optional)') }}" />
                        <select id="assigned_to" name="assigned_to" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Unassigned</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-label for="priority" value="{{ __('Priority') }}" />
                        <select id="priority" name="priority" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            @foreach(\App\Enums\HousekeepingPriority::cases() as $priority)
                                <option value="{{ $priority->value }}">{{ $priority->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-label for="status" value="{{ __('Status') }}" />
                        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            @foreach(\App\Enums\HousekeepingStatus::cases() as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-label for="due_date" value="{{ __('Due Date') }}" />
                        <x-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" required />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-button class="ml-3">
                        {{ __('Create Task') }}
                    </x-button>
                </div>
            </form>
        </div>
    </x-modal>
</x-app-layout>
