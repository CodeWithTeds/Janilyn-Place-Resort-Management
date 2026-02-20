<?php

namespace App\Services;

class DashboardService
{
    /**
     * Get dashboard statistics for the client.
     *
     * @param  \App\Models\User  $user
     * @return array
     */
    public function getClientStats($user)
    {
        // Mock data for now. In a real app, we would fetch this from repositories.
        return [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'stats' => [
                'total_orders' => 12,
                'pending_orders' => 2,
                'notifications' => 5,
            ],
            'recent_activity' => [
                [
                    'id' => 1,
                    'title' => 'Order #1234 Placed',
                    'date' => '2023-10-25 10:30 AM',
                    'status' => 'pending',
                ],
                [
                    'id' => 2,
                    'title' => 'Payment Received',
                    'date' => '2023-10-24 02:15 PM',
                    'status' => 'completed',
                ],
                [
                    'id' => 3,
                    'title' => 'Account Updated',
                    'date' => '2023-10-20 09:00 AM',
                    'status' => 'info',
                ],
            ]
        ];
    }
}
