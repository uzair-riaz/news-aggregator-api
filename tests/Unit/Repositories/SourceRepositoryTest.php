<?php

namespace Tests\Unit\Repositories;

use App\Models\Source;
use App\Repositories\SourceRepository;
use Tests\TestCase;

class SourceRepositoryTest extends TestCase
{
    protected SourceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(SourceRepository::class);
    }

    public function test_create()
    {
        $source = Source::factory()->make();
        $this->repository->create($source->getAttributes());

        $this->assertDatabaseHas('sources', $source->getAttributes());
    }

    public function test_create_duplicate()
    {
        $source = Source::factory()->create();
        $this->repository->create($source->getAttributes());

        $this->assertDatabaseCount('sources', 1);
    }
}
