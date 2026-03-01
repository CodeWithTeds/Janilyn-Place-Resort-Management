<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectUsersTo(function () {
            /** @var \App\Models\User|null $user */
            $user = Illuminate\Support\Facades\Auth::user();

            if ($user && $user->isAdmin()) {
                return '/admin/dashboard';
            }

            if ($user && $user->isOwner()) {
                return '/owner/dashboard';
            }

            if ($user && $user->isStaff()) {
                return '/staff/dashboard';
            }

            return '/dashboard';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
