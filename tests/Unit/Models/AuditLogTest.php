<?php

namespace Tests\Unit\Models;

use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_values_cast_as_json()
    {
        $log = AuditLog::factory()->create();
        $this->assertIsArray($log->new_values);
        $this->assertEquals('value', $log->new_values['field']);
    }

    public function test_auditable_relation_works()
    {
        $log = AuditLog::factory()->create();
        $this->assertNotNull($log->auditable);
    }
}
