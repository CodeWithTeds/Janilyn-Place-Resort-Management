<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    /**
     * Display the dashboard for admin and owner.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->can('access-owner-dashboard')) {
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
