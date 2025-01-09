<?php

namespace Tests\Unit\Repositories;

use App\Models\Author;
use App\Repositories\AuthorRepository;
use Tests\TestCase;

class AuthorRepositoryTest extends TestCase
{
    protected AuthorRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(AuthorRepository::class);
    }

    public function test_create()
    {
        $author = Author::factory()->make();
        $this->repository->create($author->getAttributes());

        $this->assertDatabaseHas('authors', $author->getAttributes());
    }

    public function test_create_duplicate()
    {
        $author = Author::factory()->create();
        $this->repository->create($author->getAttributes());

        $this->assertDatabaseCount('authors', 1);
    }
}
