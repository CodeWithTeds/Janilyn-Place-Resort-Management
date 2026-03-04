<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Analytics & Reporting') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Monthly Revenue -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-brand-500 transition hover:shadow-2xl">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-brand-100 text-brand-600">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Monthly Revenue</p>
                            <p class="text-2xl font-bold text-gray-900">₱{{ number_format($monthlyRevenue, 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Monthly Bookings -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-blue-500 transition hover:shadow-2xl">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Monthly Bookings</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($monthlyBookings) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Confirmed Bookings -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-green-500 transition hover:shadow-2xl">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Confirmed</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $bookingStatusStats['confirmed'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pending Bookings -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-yellow-500 transition hover:shadow-2xl">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Pending</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $bookingStatusStats['pending'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Revenue Trend Chart -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue Trend (Last 7 Days)</h3>
                    <div class="h-64">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Room Type Popularity Chart -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Room Type Popularity</h3>
                    <div class="h-64">
                        <canvas id="roomTypeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Activity / Additional Stats can go here -->

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const revenueData = @json($revenueTrend);
            
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: revenueData.map(d => d.date),
                    datasets: [{
                        label: 'Revenue (₱)',
                        data: revenueData.map(d => d.total),
                        borderColor: '#0ea5e9', // Sky-500 (Brand)
                        backgroundColor: 'rgba(14, 165, 233, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value;
                                }
                            }
                        }
                    }
                }
            });

            // Room Type Chart
            const roomCtx = document.getElementById('roomTypeChart').getContext('2d');
            const roomData = @json($roomTypePopularity);
            
            new Chart(roomCtx, {
                type: 'doughnut',
                data: {
                    labels: roomData.map(d => d.room_type ? d.room_type.name : 'Unknown'),
                    datasets: [{
                        data: roomData.map(d => d.total),
                        backgroundColor: [
                            '#0ea5e9', // Sky-500
                            '#3b82f6', // Blue-500
                            '#6366f1', // Indigo-500
                            '#8b5cf6', // Violet-500
                            '#a855f7', // Purple-500
                            '#d946ef', // Fuchsia-500
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
