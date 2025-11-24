<?php

namespace Tests\Unit\Services;

use App\Models\AuditLog;
use App\Repositories\Interfaces\AuditLogRepositoryInterface;
use App\Services\AuditLogService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class DummyModel extends Model
{
    protected $table = 'dummy';
    protected $guarded = [];
}

class AuditLogServiceTest extends TestCase
{
    use WithFaker;

    protected $repo;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Fake request() helper
        $request = Request::create('/', 'GET', [], [], [], [
            'REMOTE_ADDR' => '123.45.67.89',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 TestAgent'
        ]);
        $this->app->instance('request', $request);

        // Mock repository
        $this->repo = Mockery::mock(AuditLogRepositoryInterface::class);
        $this->service = new AuditLogService($this->repo);

        // Fake Auth::id()
        Auth::shouldReceive('id')->andReturn(10);
    }

    public function test_it_creates_audit_log_correctly()
    {
        $model = new DummyModel();
        $model->id = 99;

        $old = [
            'name' => 'Old Name',
            'password' => '123456' // sensitive, phải bị remove
        ];

        $new = [
            'name' => 'New Name',
            'wallet_number' => 'ABC123', // sensitive, remove
        ];

        // Expected AuditLog model
        $expected = new AuditLog([
            'user_id'        => 10,
            'action'         => 'update',
            'auditable_type' => $model->getMorphClass(),
            'auditable_id'   => 99,
            'old_values'     => ['name' => 'Old Name'],   // sanitized
            'new_values'     => ['name' => 'New Name'],   // sanitized
            'description'    => 'Updated name',
            'ip_address'             => '123.45.67.89',
            'user_agent'     => 'Mozilla/5.0 TestAgent',
        ]);

        // Expect repo.create() nhận đúng dữ liệu
        $this->repo
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) use ($model) {

                return
                    $data['user_id']        === 10 &&
                    $data['action']         === 'update' &&
                    $data['auditable_type'] === $model->getMorphClass() &&
                    $data['auditable_id']   === 99 &&

                    // Sensitive removed
                    !isset($data['old_values']['password']) &&
                    !isset($data['new_values']['wallet_number']) &&

                    $data['old_values']['name'] === 'Old Name' &&
                    $data['new_values']['name'] === 'New Name' &&

                    // IP + UA
                    $data['ip_address'] === '123.45.67.89' &&
                    $data['user_agent'] === 'Mozilla/5.0 TestAgent';
            }))
            ->andReturn($expected);

        $result = $this->service->log(
            action: 'update',
            model: $model,
            oldValues: $old,
            newValues: $new,
            description: 'Updated name'
        );

        // Assertions
        $this->assertInstanceOf(AuditLog::class, $result);
        $this->assertEquals(10, $result->user_id);
        $this->assertEquals('update', $result->action);
        $this->assertEquals($model->getMorphClass(), $result->auditable_type);
        $this->assertEquals(99, $result->auditable_id);
        $this->assertEquals(['name' => 'Old Name'], $result->old_values);
        $this->assertEquals(['name' => 'New Name'], $result->new_values);
        $this->assertEquals('Updated name', $result->description);
        $this->assertEquals('123.45.67.89', $result->ip_address);
        $this->assertEquals('Mozilla/5.0 TestAgent', $result->user_agent);
    }
}
