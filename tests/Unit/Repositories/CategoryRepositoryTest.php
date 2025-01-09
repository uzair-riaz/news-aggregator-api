<?php

namespace Tests\Unit\Repositories;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Tests\TestCase;

class CategoryRepositoryTest extends TestCase
{
    protected CategoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CategoryRepository::class);
    }

    public function test_create()
    {
        $category = Category::factory()->make();
        $this->repository->create($category->getAttributes());

        $this->assertDatabaseHas('categories', $category->getAttributes());
    }

    public function test_create_duplicate()
    {
        $category = Category::factory()->create();
        $this->repository->create($category->getAttributes());

        $this->assertDatabaseCount('categories', 1);
    }
}
