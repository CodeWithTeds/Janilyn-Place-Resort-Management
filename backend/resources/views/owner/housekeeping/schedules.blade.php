<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Room Cleaning Schedules') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{
        scheduleForm: {
            unit_id: '',
            unit_name: '',
            title: '',
            description: '',
            assigned_to: '',
            priority: 'medium',
            due_date: '{{ date('Y-m-d') }}'
        },
        openScheduleModal(unitId, unitName) {
            this.scheduleForm.unit_id = unitId;
            this.scheduleForm.unit_name = unitName;
            this.scheduleForm.title = 'Cleaning for ' + unitName;
            $dispatch('open-modal', 'schedule-cleaning-modal');
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-4 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Unit Cleaning Status & Schedules</h3>
                    <a href="{{ route('owner.housekeeping.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">View All Tasks &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cleaning Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Scheduled Cleaning</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($units as $unit)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $unit->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $unit->roomType->name ?? 'Unknown Type' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form action="{{ route('owner.housekeeping.units.status', $unit) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select name="cleaning_status" onchange="this.form.submit()" class="text-xs rounded-full border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                                                {{ match($unit->cleaning_status->value) {
                                                    'clean' => 'bg-green-100 text-green-800',
                                                    'dirty' => 'bg-red-100 text-red-800',
                                                    'cleaning' => 'bg-blue-100 text-blue-800',
                                                    'inspection_ready' => 'bg-indigo-100 text-indigo-800',
                                                    'do_not_disturb' => 'bg-gray-100 text-gray-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                } }}">
                                                @foreach(\App\Enums\UnitCleaningStatus::cases() as $status)
                                                    <option value="{{ $status->value }}" {{ $unit->cleaning_status === $status ? 'selected' : '' }}>
                                                        {{ $status->label() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($unit->housekeepingTasks->isNotEmpty())
                                            @php $nextTask = $unit->housekeepingTasks->first(); @endphp
                                            <div class="text-sm text-gray-900 font-medium">{{ $nextTask->due_date->format('M d, Y') }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $nextTask->assignee->name ?? 'Unassigned' }} 
                                                <span class="ml-1 text-{{ $nextTask->priority === \App\Enums\HousekeepingPriority::URGENT ? 'red' : 'gray' }}-500">
                                                    ({{ $nextTask->priority->label() }})
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400 italic">No upcoming tasks</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="openScheduleModal({{ $unit->id }}, '{{ addslashes($unit->name) }}')" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                            Schedule Cleaning
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                        No resort units found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Schedule Cleaning Modal -->
        <x-modal id="schedule-cleaning-modal" focusable>
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Schedule Cleaning for <span x-text="scheduleForm.unit_name"></span>
                </h2>

                <form method="POST" action="{{ route('owner.housekeeping.tasks.store') }}" class="mt-6">
                    @csrf
                    <input type="hidden" name="resort_unit_id" x-model="scheduleForm.unit_id">
                    <input type="hidden" name="status" value="pending">

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-label for="title" value="{{ __('Task Title') }}" />
                            <x-input id="title" class="block mt-1 w-full" type="text" name="title" x-model="scheduleForm.title" required />
                        </div>

                        <div>
                            <x-label for="description" value="{{ __('Description (Optional)') }}" />
                            <textarea id="description" name="description" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="3" x-model="scheduleForm.description"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-label for="due_date" value="{{ __('Due Date') }}" />
                                <x-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" x-model="scheduleForm.due_date" required />
                            </div>
                            
                            <div>
                                <x-label for="priority" value="{{ __('Priority') }}" />
                                <select id="priority" name="priority" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" x-model="scheduleForm.priority" required>
                                    @foreach(\App\Enums\HousekeepingPriority::cases() as $priority)
                                        <option value="{{ $priority->value }}">{{ $priority->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <x-label for="assigned_to" value="{{ __('Assign To (Optional)') }}" />
                            <select id="assigned_to" name="assigned_to" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" x-model="scheduleForm.assigned_to">
                                <option value="">Unassigned</option>
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-secondary-button x-on:click="$dispatch('close')" class="mr-3">
                            {{ __('Cancel') }}
                        </x-secondary-button>

                        <x-button>
                            {{ __('Create Schedule') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>
