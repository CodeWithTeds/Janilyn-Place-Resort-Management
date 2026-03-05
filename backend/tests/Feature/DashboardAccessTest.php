<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    // /dashboard
    public function test_admin_is_redirected_from_dashboard_to_admin_dashboard()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect('/admin/dashboard');
    }

    public function test_owner_is_redirected_from_dashboard_to_owner_analytics()
    {
        $user = User::factory()->create(['role' => UserRole::OWNER]);
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect('/owner/analytics');
    }

    public function test_staff_cannot_access_dashboard()
    {
        $user = User::factory()->create(['role' => UserRole::STAFF]);
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(403);
    }

    // /admin/dashboard
    public function test_admin_can_access_admin_dashboard()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_owner_cannot_access_admin_dashboard()
    {
        $user = User::factory()->create(['role' => UserRole::OWNER]);
        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_staff_cannot_access_admin_dashboard()
    {
        $user = User::factory()->create(['role' => UserRole::STAFF]);
        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    // /owner/dashboard
    public function test_owner_is_redirected_from_owner_dashboard_to_owner_analytics()
    {
        $user = User::factory()->create(['role' => UserRole::OWNER]);
        $response = $this->actingAs($user)->get('/owner/dashboard');
        $response->assertRedirect('/owner/analytics');
    }

    public function test_admin_is_redirected_from_owner_dashboard_to_admin_dashboard()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $response = $this->actingAs($user)->get('/owner/dashboard');
        $response->assertRedirect('/admin/dashboard');
    }

    public function test_staff_cannot_access_owner_dashboard()
    {
        $user = User::factory()->create(['role' => UserRole::STAFF]);
        $response = $this->actingAs($user)->get('/owner/dashboard');
        $response->assertStatus(403);
    }
}
