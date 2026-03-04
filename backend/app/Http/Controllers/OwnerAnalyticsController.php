<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OwnerAnalyticsController extends Controller
{
    public function index(): View
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Total Revenue for current month (excluding cancelled)
        $monthlyRevenue = Booking::whereBetween('check_in', [$startDate, $endDate])
            ->where('status', '!=', BookingStatus::CANCELLED)
            ->sum('total_price');

        // Total Bookings for current month
        $monthlyBookings = Booking::whereBetween('created_at', [$startDate, $endDate])->count();

        // Booking Status Breakdown
        $bookingStatusStats = Booking::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status->value => $item->total];
            });

        // Revenue Trend (Last 7 days)
        $revenueTrend = Booking::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as total'))
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->where('status', '!=', BookingStatus::CANCELLED)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Room Type Popularity
        $roomTypePopularity = Booking::select('room_type_id', DB::raw('count(*) as total'))
            ->whereNotNull('room_type_id')
            ->groupBy('room_type_id')
            ->with('roomType')
            ->get();

        return view('owner.analytics.index', compact(
            'monthlyRevenue',
            'monthlyBookings',
            'bookingStatusStats',
            'revenueTrend',
            'roomTypePopularity'
        ));
    }
}
