<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard for admin and owner.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user && $user->isOwner()) {
            return redirect()->route('owner.analytics.index');
        }

        return view('dashboard');
    }

    /**
     * Display the admin specific dashboard.
     */
    public function admin(): View
    {
        return view('dashboard'); // Or a specific admin dashboard view
    }

    /**
     * Display the owner specific dashboard.
     */
    public function owner(): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('owner.analytics.index');
    }

    /**
     * Display the staff specific dashboard.
     */
    public function staff(): View
    {
        return view('dashboard'); // Or a specific staff dashboard view
    }
}
