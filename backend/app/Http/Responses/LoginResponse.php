<?php

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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
        $intendedUrl = $request->session()->pull('url.intended');

        if ($user instanceof User) {
            if ($user->isAdmin()) {
                return $this->redirectForUser($user, $intendedUrl, '/admin/dashboard');
            }

            if ($user->isOwner()) {
                return $this->redirectForUser($user, $intendedUrl, '/owner/dashboard');
            }

            if ($user->isStaff()) {
                return $this->redirectForUser($user, $intendedUrl, '/staff/dashboard');
            }
        }

        if ($intendedUrl) {
            return redirect()->to($intendedUrl);
        }

        return redirect()->to(config('fortify.home'));
    }

    private function redirectForUser(User $user, ?string $intendedUrl, string $fallbackUrl)
    {
        if ($intendedUrl && $this->canAccessIntendedUrl($user, $intendedUrl)) {
            return redirect()->to($intendedUrl);
        }

        return redirect()->to($fallbackUrl);
    }

    private function canAccessIntendedUrl(User $user, string $intendedUrl): bool
    {
        $path = parse_url($intendedUrl, PHP_URL_PATH) ?? '';

        if ($path === '') {
            return false;
        }

        if (Str::startsWith($path, '/admin')) {
            return $user->can('access-admin-dashboard');
        }

        if (Str::startsWith($path, '/owner')) {
            return $user->can('access-owner-dashboard');
        }

        if (Str::startsWith($path, '/staff')) {
            return $user->can('access-staff-dashboard');
        }

        if (Str::startsWith($path, '/resort-management')) {
            return $user->can('access-resort-management');
        }

        return true;
    }
}
