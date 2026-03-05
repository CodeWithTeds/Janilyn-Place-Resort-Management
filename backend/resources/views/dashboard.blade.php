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

            @if(Auth::user()?->isStaff())
                <div class="mt-6 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">My Staff Tasks</h3>
                        <p class="mt-1 text-sm text-gray-600">Tasks assigned from Staff Management.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned By</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse(($assignedTasks ?? collect()) as $task)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                            @if($task->description)
                                                <div class="text-sm text-gray-500 mt-1">{{ $task->description }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($task->priority) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                {{ $task->status === 'pending' ? 'bg-gray-100 text-gray-800' : '' }}
                                                {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $task->due_date ? $task->due_date->format('M d, Y h:i A') : 'No due date' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $task->createdBy?->name ?? 'System' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No assigned work yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(isset($assignedTasks) && method_exists($assignedTasks, 'links'))
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $assignedTasks->links() }}
                        </div>
                    @endif
                </div>

                <div class="mt-6 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">My Housekeeping Assignments</h3>
                        <p class="mt-1 text-sm text-gray-600">Tasks assigned from Housekeeping schedules.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse(($housekeepingAssignedTasks ?? collect()) as $task)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                            @if($task->description)
                                                <div class="text-sm text-gray-500 mt-1">{{ $task->description }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $task->resortUnit?->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $task->priority->label() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                {{ $task->status->value === 'pending' ? 'bg-gray-100 text-gray-800' : '' }}
                                                {{ $task->status->value === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $task->status->value === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $task->status->value === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ $task->status->label() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No housekeeping assignment yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(isset($housekeepingAssignedTasks) && method_exists($housekeepingAssignedTasks, 'links'))
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $housekeepingAssignedTasks->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
