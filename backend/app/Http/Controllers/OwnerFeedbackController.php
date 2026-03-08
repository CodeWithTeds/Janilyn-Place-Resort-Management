<?php

namespace App\Http\Controllers;

use App\Models\BookingFeedback;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerFeedbackController extends Controller
{
    public function index(Request $request): View
    {
        $feedback = BookingFeedback::with(['booking.roomType', 'booking.exclusiveResortRental'])
            ->latest()
            ->paginate(20);

        return view('owner.resort-management.feedback.index', compact('feedback'));
    }
}
