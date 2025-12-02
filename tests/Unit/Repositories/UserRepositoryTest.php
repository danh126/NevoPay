<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone_number' => '1234567890',
            'password' => 'secret123',
            'role' => 'user',
            'is_active' => true,
            'towo_factor_enabled' => false,
        ];

        $user = $this->repo->create($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->full_name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('1234567890', $user->phone_number);
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
        $user = User::factory()->create(['full_name' => 'Old Name']);
        $updated = $this->repo->update($user->id, ['full_name' => 'New Name']);

        $this->assertEquals('New Name', $updated->full_name);
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
}
