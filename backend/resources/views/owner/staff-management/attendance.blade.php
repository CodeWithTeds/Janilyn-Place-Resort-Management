<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Staff Attendance') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @include('owner.staff-management.nav')

            <!-- Add Attendance Form -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Record Attendance</h3>
                <form method="POST" action="{{ route('owner.staff-management.attendance.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <x-label for="user_id" value="{{ __('Staff Member') }}" />
                            <select id="user_id" name="user_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-label for="date" value="{{ __('Date') }}" />
                            <x-input id="date" class="block mt-1 w-full" type="date" name="date" required />
                        </div>
                        <div>
                            <x-label for="clock_in" value="{{ __('Clock In') }}" />
                            <x-input id="clock_in" class="block mt-1 w-full" type="datetime-local" name="clock_in" />
                        </div>
                        <div>
                            <x-label for="clock_out" value="{{ __('Clock Out') }}" />
                            <x-input id="clock_out" class="block mt-1 w-full" type="datetime-local" name="clock_out" />
                        </div>
                        <div>
                            <x-label for="status" value="{{ __('Status') }}" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="present" selected>Present</option>
                                <option value="late">Late</option>
                                <option value="absent">Absent</option>
                                <option value="leave">Leave</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <x-label for="notes" value="{{ __('Notes') }}" />
                        <textarea id="notes" name="notes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <x-button>
                            {{ __('Record Attendance') }}
                        </x-button>
                    </div>
                </form>
            </div>

            <!-- Attendance List -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock In</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock Out</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($attendances as $attendance)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $attendance->user->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $attendance->date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $attendance->clock_in ? $attendance->clock_in->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $attendance->status === 'present' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $attendance->status === 'late' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $attendance->status === 'absent' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ \Illuminate\Support\Str::limit($attendance->notes, 50) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                        No attendance records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="px-6 py-4">
                        {{ $attendances->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
