<?php

namespace Tests\Unit\Repositories;

use App\Models\AuditLog;
use App\Models\User;
use App\Repositories\AuditLogRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

class AuditLogRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected AuditLogRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new AuditLogRepository();
    }

    public function test_create_audit_log()
    {
        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'action' => 'update',
            'auditable_type' => AuditLog::class,
            'auditable_id' => 1,
            'old_values' => json_encode(['field1' => 'old']),
            'new_values' => json_encode(['field1' => 'new']),
            'description' => 'Updated field1',
        ];

        $log = $this->repo->create($data);

        $this->assertNotNull($log);
        $this->assertDatabaseHas('audit_logs', [
            'id' => $log->id,
            'user_id' => $user->id,
            'action' => 'update',
        ]);
    }

    public function test_find_audit_log()
    {
        $log = AuditLog::factory()->create();
        $found = $this->repo->find($log->id);

        $this->assertNotNull($found);
        $this->assertEquals($log->id, $found->id);
    }

    public function test_update_audit_log()
    {
        $log = AuditLog::factory()->create(['description' => 'Old desc']);

        $updated = $this->repo->update($log->id, ['description' => 'New desc']);

        $this->assertEquals('New desc', $updated->description);
        $this->assertDatabaseHas('audit_logs', [
            'id' => $log->id,
            'description' => 'New desc',
        ]);
    }

    public function test_delete_audit_log()
    {
        $log = AuditLog::factory()->create();

        $result = $this->repo->delete($log->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('audit_logs', ['id' => $log->id]);
    }

    public function test_filter_audit_logs_by_user_id()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $log1 = AuditLog::factory()->create(['user_id' => $user1->id]);
        $log2 = AuditLog::factory()->create(['user_id' => $user2->id]);

        $results = $this->repo->filter(['user_id' => $user1->id]);

        $collection = collect($results->items());
        $this->assertTrue($collection->contains('id', $log1->id));
        $this->assertFalse($collection->contains('id', $log2->id));
    }

    public function test_filter_audit_logs_by_action_and_auditable_type()
    {
        $log1 = AuditLog::factory()->create([
            'action' => 'create',
            'auditable_type' => AuditLog::class,
        ]);
        $log2 = AuditLog::factory()->create([
            'action' => 'update',
            'auditable_type' => AuditLog::class,
        ]);

        $results = $this->repo->filter([
            'action' => 'create',
            'auditable_type' => AuditLog::class,
        ]);

        $collection = collect($results->items());
        $this->assertTrue($collection->contains('id', $log1->id));
        $this->assertFalse($collection->contains('id', $log2->id));
    }

    public function test_filter_audit_logs_by_date_range()
    {
        $log1 = AuditLog::factory()->create(['created_at' => now()->subDays(3)]);
        $log2 = AuditLog::factory()->create(['created_at' => now()->subDays(1)]);

        $results = $this->repo->filter([
            'date_from' => now()->subDays(2)->toDateString(),
            'date_to' => now()->toDateString(),
        ]);

        $collection = collect($results->items());
        $this->assertFalse($collection->contains('id', $log1->id));
        $this->assertTrue($collection->contains('id', $log2->id));
    }
}
