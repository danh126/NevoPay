<?php

namespace Tests\Feature\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $normalUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Tạo admin và user bình thường
        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->normalUser = User::factory()->create();
    }

    public function test_admin_can_access_audit_logs_index()
    {
        AuditLog::factory()->count(5)->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
                         ->getJson('/api/admin/audit-logs');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data', 'current_page', 'last_page', 'per_page', 'total'
                     ]
        ]);
    }

    public function test_non_admin_cannot_access_audit_logs_index()
    {
        $response = $this->actingAs($this->normalUser, 'sanctum')
                         ->getJson('/api/admin/audit-logs');

        $response->assertStatus(403)
                 ->assertJson(['success' => false]);
    }

    public function test_admin_can_view_single_audit_log()
    {
        $log = AuditLog::factory()->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
                         ->getJson("/api/admin/audit-logs/{$log->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => ['id' => $log->id]
        ]);
    }

    public function test_admin_gets_not_found_for_missing_audit_log()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
                         ->getJson("/api/admin/audit-logs/9999");

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Audit log not found'
        ]);
    }
}
