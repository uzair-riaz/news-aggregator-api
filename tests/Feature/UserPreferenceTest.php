<?php

namespace Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use App\Models\User;
use App\Models\UserPreference;
use Tests\FeatureTestCase;

class UserPreferenceTest extends FeatureTestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_get_user_preferences()
    {
        UserPreference::factory()->create(['user_id' => $this->user->id]);
        $response = $this->actingAs($this->user)->get('/api/preferences');

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'preferences' => [
                'id',
                'userId',
                'preferences' => [
                    'source_ids',
                    'category_ids',
                    'author_ids'
                ]
            ]
        ]);
    }

    public function test_save_user_preferences_validation_errors()
    {
        $response = $this->actingAs($this->user)->post(
            '/api/preferences',
            [
                'preferences' => [
                    'source_ids' => [1],
                    'category_ids' => [2],
                    'author_ids' => []
                ]
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "preferences.source_ids.0" => [
                "The selected preferences.source_ids.0 is invalid."
            ],
            "preferences.category_ids.0" => [
                "The selected preferences.category_ids.0 is invalid."
            ]
        ]);
    }

    public function test_save_user_preferences_successful()
    {
        $source = Source::factory()->create();
        $category = Category::factory()->create();
        $response = $this->actingAs($this->user)->post(
            '/api/preferences',
            [
                'preferences' => [
                    'source_ids' => [$source->id],
                    'category_ids' => [$category->id],
                    'author_ids' => []
                ]
            ]
        );

        $response->assertCreated();
        $this->assertDatabaseHas('user_preferences', ['user_id' => $this->user->id]);
    }

    public function test_personalized_news_feed_preferences_not_found()
    {
        $response = $this->actingAs($this->user)->get('/api/user/personalized-feed');

        $response->assertNotFound();
    }

    public function test_personalized_news_feed_successful()
    {
        $articles = Article::factory()->createMany(5)->values();
        $article1 = $articles->get(0);
        $article3 = $articles->get(2);
        $article5 = $articles->get(4);

        UserPreference::factory()->create([
            'user_id' => $this->user->id,
            'preferences' => [
                'source_ids' => [$article1->source->id, $article3->source->id, $article5->source->id],
                'category_ids' => [$article1->category->id, $article3->category->id],
                'author_ids' => [$article1->author->id, $article3->author->id]
            ]
        ]);

        $response = $this->actingAs($this->user)->get('/api/user/personalized-feed');

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'articles' => [
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'url',
                        'description',
                        'published_at',
                        'source',
                        'category',
                        'author'
                    ]
                ]
            ]
        ]);
        $data = $response['articles']['data'];
        $this->assertCount(2, $data);
        $this->assertEquals($article1->id, $data[0]['id']);
        $this->assertEquals($article3->id, $data[1]['id']);
    }
}
