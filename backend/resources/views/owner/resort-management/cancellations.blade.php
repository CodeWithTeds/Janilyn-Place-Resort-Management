<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cancellation and Refund Processing') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Cancellation Requests</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Refund Amount</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">#BK-12345</td>
                                <td class="px-6 py-4 whitespace-nowrap">Alice Wonderland</td>
                                <td class="px-6 py-4 whitespace-nowrap">Mar 1 - Mar 5</td>
                                <td class="px-6 py-4 whitespace-nowrap">$250.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded mr-2">Process Refund</button>
                                    <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-3 rounded">Reject</button>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">#BK-67890</td>
                                <td class="px-6 py-4 whitespace-nowrap">Bob Builder</td>
                                <td class="px-6 py-4 whitespace-nowrap">Feb 20 - Feb 22</td>
                                <td class="px-6 py-4 whitespace-nowrap">$0.00 (Non-refundable)</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded">Confirm Cancellation</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
