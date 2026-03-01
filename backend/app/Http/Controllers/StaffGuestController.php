<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class StaffGuestController extends Controller
{
    /**
     * Display a listing of guests.
     */
    public function index()
    {
        $guests = User::where('role', '!=', UserRole::ADMIN)
                      ->where('role', '!=', UserRole::STAFF)
                      ->where('role', '!=', UserRole::OWNER)
                      ->paginate(10);
        return view('staff.guests.index', compact('guests'));
    }

    /**
     * Show the form for creating a new guest.
     */
    public function create()
    {
        return view('staff.guests.create');
    }

    /**
     * Store a newly created guest in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'role' => UserRole::GUEST,
        ]);

        return redirect()->route('staff.guests.index')->with('success', 'Guest registered successfully.');
    }

    /**
     * Show the form for editing the specified guest.
     */
    public function edit(User $guest)
    {
        return view('staff.guests.edit', compact('guest'));
    }

    /**
     * Update the specified guest in storage.
     */
    public function update(Request $request, User $guest)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$guest->id],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ]);

        $guest->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        return redirect()->route('staff.guests.index')->with('success', 'Guest information updated.');
    }
}
