<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_redirected_to_admin_dashboard_after_login()
    {
        $user = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_admin_is_not_redirected_to_owner_intended_url_after_login()
    {
        $user = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $session = $this->app['session.store'];
        $session->put('url.intended', 'http://127.0.0.1:8000/owner/room-inspections');

        $response = $this->withSession([
            'url.intended' => $session->get('url.intended'),
        ])->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_owner_is_redirected_to_owner_dashboard_after_login()
    {
        $user = User::factory()->create([
            'role' => UserRole::OWNER,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/owner/dashboard');
    }

    public function test_staff_is_redirected_to_staff_dashboard_after_login()
    {
        $user = User::factory()->create([
            'role' => UserRole::STAFF,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/staff/dashboard');
    }
}
