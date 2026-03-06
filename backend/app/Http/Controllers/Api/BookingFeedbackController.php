<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingFeedback;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BookingFeedbackController extends Controller
{
    public function show(Request $request, Booking $booking): JsonResponse
    {
        $user = $request->user();

        if ((int) $booking->user_id !== (int) $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $feedback = BookingFeedback::where('booking_id', $booking->id)
            ->where('user_id', $user->id)
            ->first();

        return response()->json([
            'has_feedback' => (bool) $feedback,
            'feedback' => $feedback ? [
                'rating' => $feedback->rating,
                'comment' => $feedback->comment,
                'created_at' => $feedback->created_at,
            ] : null,
        ]);
    }

    public function store(Request $request, Booking $booking): JsonResponse
    {
        $user = $request->user();

        if ((int) $booking->user_id !== (int) $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($booking->status->value !== 'confirmed' || $booking->payment_status->value !== 'paid') {
            return response()->json(['message' => 'Feedback allowed only for confirmed and paid bookings'], 422);
        }

        $validated = $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'required|string|max:2000',
        ]);

        $existing = BookingFeedback::where('booking_id', $booking->id)
            ->where('user_id', $user->id)
            ->exists();
        if ($existing) {
            return response()->json(['message' => 'Feedback already submitted for this booking'], 409);
        }

        try {
            $feedback = BookingFeedback::create([
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'rating' => $validated['rating'] ?? null,
                'comment' => $validated['comment'],
            ]);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Feedback already submitted for this booking'], 409);
        }

        return response()->json([
            'message' => 'Feedback submitted successfully',
            'feedback' => [
                'rating' => $feedback->rating,
                'comment' => $feedback->comment,
                'created_at' => $feedback->created_at,
            ],
        ], 201);
    }
}
