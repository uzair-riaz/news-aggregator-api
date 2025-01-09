<?php

namespace Tests\Unit\Repositories;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use Tests\TestCase;

class ArticleRepositoryTest extends TestCase
{
    protected ArticleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);
    }

    public function test_create_if_does_not_exist()
    {
        $article = Article::factory()->make();
        $this->repository->create($article->getAttributes());

        $this->assertDatabaseHas('articles', $article->getAttributes());
    }

    public function test_update_if_already_exists()
    {
        $article = Article::factory()->create();
        $this->repository->create($article->getAttributes());

        $this->assertDatabaseCount('articles', 1);
    }

    public function test_filter()
    {
        Article::factory()->create(['title' => 'keyword is in the title', 'published_at' => '2025-01-01']);
        $expected = Article::factory()->create(['description' => 'keyword is in the description', 'published_at' => '2025-01-05']);
        Article::factory()->create([
            'title' => 'not in the title',
            'description' => 'not in the description',
            'published_at' => '2025-01-10',
        ]);
        $articles = $this->repository->filter([
            'keyword' => 'keyword',
            'date' => '2025-01-05'
        ], 1, 10)->items();

        $this->assertCount(1, $articles);
        $this->assertEquals($expected->id, $articles[0]->id);
    }
}
