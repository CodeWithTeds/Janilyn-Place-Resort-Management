<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerResortManagementController extends Controller
{
    public function bookings(): View
    {
        return view('owner.resort-management.bookings');
    }

    public function calendar(): View
    {
        return view('owner.resort-management.calendar');
    }

    public function checkInOut(): View
    {
        return view('owner.resort-management.check-in-out');
    }

    public function cancellations(): View
    {
        return view('owner.resort-management.cancellations');
    }
}
