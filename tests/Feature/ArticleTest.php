<?php

namespace Feature;

use App\Models\Article;
use App\Models\User;
use Tests\FeatureTestCase;

class ArticleTest extends FeatureTestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_list_articles()
    {
        Article::factory()->create(['title' => 'keyword is in the title', 'published_at' => '2025-01-01']);
        $expected = Article::factory()->create(['description' => 'keyword is in the description', 'published_at' => '2025-01-05']);
        Article::factory()->create([
            'title' => 'not in the title',
            'description' => 'not in the description',
            'published_at' => '2025-01-10',
        ]);

        $filters = [
            'filters' => [
                'keyword' => 'keyword',
                'date' => '2025-01-05'
            ]
        ];
        $response = $this->actingAs($this->user)->get('/api/articles?' . http_build_query($filters));

        $response->assertSuccessful();
        $data = $response->json('articles.data');
        $this->assertCount(1, $data);
        $this->assertEquals($expected->id, $data[0]['id']);
    }
}
