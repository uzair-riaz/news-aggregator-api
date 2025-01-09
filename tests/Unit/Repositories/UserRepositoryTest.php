<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Repositories\UserRepository;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    protected UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(UserRepository::class);
    }

    public function test_create()
    {
        $user = User::factory()->make();
        $this->repository->create($user->getAttributes());

        $this->assertDatabaseHas('users', ['email' => $user->email]);
    }

    public function test_create_duplicate()
    {
        $user = User::factory()->create();
        $this->repository->create($user->getAttributes());

        $this->assertDatabaseHas('users', ['email' => $user->email]);
    }

    public function test_get_by_email()
    {
        User::factory()->create(['email' => 'johndoe@example.com']);
        $user = $this->repository->getByEmail('johndoe@example.com');

        $this->assertEquals($user->email, 'johndoe@example.com');
    }

    public function test_update_by_email()
    {
        User::factory()->create(['email' => 'johndoe@example.com', 'password' => 'password']);
        $this->repository->updateByEmail('johndoe@example.com', ['password' => 'new_password']);
        $user = $this->repository->getByEmail('johndoe@example.com');

        $this->assertEquals($user->password, 'new_password');
    }
}
