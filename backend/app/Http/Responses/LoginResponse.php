<?php

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $user = Auth::user();

        if ($user instanceof User) {
            if ($user->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            }

            if ($user->isOwner()) {
                return redirect()->intended('/owner/dashboard');
            }

            if ($user->isStaff()) {
                return redirect()->intended('/staff/dashboard');
            }
        }

        return redirect()->intended(config('fortify.home'));
    }
}
