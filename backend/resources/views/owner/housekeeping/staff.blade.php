<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Staff Management') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="staffManagement('{{ route('owner.housekeeping.staff.destroy', 'PLACEHOLDER') }}')">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between mb-6">
                <a href="{{ route('owner.housekeeping.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                    &larr; Back to Housekeeping
                </a>
                <a href="{{ route('owner.housekeeping.staff.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Add New Staff
                </a>
            </div>

            @if (session('success'))
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-4 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 font-medium text-sm text-red-600 bg-red-100 p-4 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined Date</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($staffMembers as $staff)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $staff->profile_photo_url }}" alt="{{ $staff->name }}" />
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $staff->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $staff->email }}</div>
                                        <div class="text-sm text-gray-500">{{ $staff->phone_number ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                            {{ ucfirst($staff->role->value ?? 'Staff') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $staff->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('owner.housekeeping.staff.edit', $staff) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <button @click="openDelete({{ $staff->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                        No staff members found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- Delete Staff Modal -->
        <x-modal id="delete-staff-modal" focusable>
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Delete Staff Account') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Are you sure you want to delete this staff account? This action cannot be undone.') }}
                </p>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')" class="mr-3">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <form method="POST" :action="deleteUrl" class="inline">
                        @csrf
                        @method('DELETE')
                        <x-danger-button type="submit">
                            {{ __('Delete Staff') }}
                        </x-danger-button>
                    </form>
                </div>
            </div>
        </x-modal>
    </div>
</x-app-layout>