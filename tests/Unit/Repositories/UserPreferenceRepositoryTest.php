<?php

namespace Tests\Unit\Repositories;

use App\Models\UserPreference;
use App\Repositories\UserPreferenceRepository;
use Tests\TestCase;

class UserPreferenceRepositoryTest extends TestCase
{
    protected UserPreferenceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(UserPreferenceRepository::class);
    }

    public function test_create()
    {
        $userPreference = UserPreference::factory()->make();
        $this->repository->create($userPreference->getAttributes());

        $this->assertDatabaseHas('user_preferences', $userPreference->getAttributes());
    }

    public function test_create_duplicate()
    {
        $userPreference = UserPreference::factory()->create();
        $this->repository->create($userPreference->getAttributes());

        $this->assertDatabaseCount('user_preferences', 1);
    }
}
