<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new UserRepository();
    }

    public function test_create_user_hashes_password()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
            'role' => 'user',
            'is_active' => true,
        ];

        $user = $this->repo->create($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_find_user_by_id()
    {
        $user = User::factory()->create();
        $found = $this->repo->find($user->id);

        $this->assertNotNull($found);
        $this->assertEquals($user->id, $found->id);
    }

    public function test_find_user_by_email()
    {
        $user = User::factory()->create();
        $found = $this->repo->findByEmail($user->email);

        $this->assertNotNull($found);
        $this->assertEquals($user->email, $found->email);
    }

    public function test_update_user()
    {
        $user = User::factory()->create(['name' => 'Old Name']);
        $updated = $this->repo->update($user->id, ['name' => 'New Name']);

        $this->assertEquals('New Name', $updated->name);
    }

    public function test_delete_user()
    {
        $user = User::factory()->create();
        $result = $this->repo->delete($user->id);

        $this->assertTrue($result);
        $this->assertNull(User::find($user->id));
    }

    public function test_is_active_method()
    {
        $user = User::factory()->create(['is_active' => true]);
        $this->assertTrue($this->repo->isActive($user->id));

        $user->update(['is_active' => false]);
        $this->assertFalse($this->repo->isActive($user->id));
    }

    public function test_exists_by_email_method()
    {
        $user = User::factory()->create();
        $this->assertTrue($this->repo->existsByEmail($user->email));
        $this->assertFalse($this->repo->existsByEmail('notfound@example.com'));
    }

    public function test_update_nonexistent_user_returns_null()
    {
        $updated = $this->repo->update(99999, ['name' => 'New Name']);
        $this->assertNull($updated);
    }

    public function test_delete_nonexistent_user_returns_false()
    {
        $result = $this->repo->delete(99999);
        $this->assertFalse($result);
    }
}
