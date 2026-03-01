<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard for admin and owner.
     */
    public function index(): View
    {
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
    public function owner(): View
    {
        return view('dashboard'); // Or a specific owner dashboard view
    }

    /**
     * Display the staff specific dashboard.
     */
    public function staff(): View
    {
        return view('dashboard'); // Or a specific staff dashboard view
    }
}
