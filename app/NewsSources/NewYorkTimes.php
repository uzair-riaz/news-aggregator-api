<?php

namespace App\NewsSources;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class NewYorkTimes implements NewsSource
{
    protected $apiKey;
    protected $pageSize;

    public function __construct($apiKey, $pageSize)
    {
        $this->apiKey = $apiKey;
        $this->pageSize = $pageSize;
    }

    public function fetchNews($page = 1)
    {
        $client = new Client(['base_uri' => 'https://api.nytimes.com/']);

        try {
            $response = $client->get('svc/search/v2/articlesearch.json', [
                RequestOptions::QUERY => [
                    'api-key' => $this->apiKey,
                    'q' => null,
                    'begin_date' => Carbon::yesterday()->toDateString(),
                    'end_date' => Carbon::today()->toDateString(),
                    'sort' => 'relevance',
                    'page' => $page,
                ]
            ]);
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Failed to fetch news from New York Times API: ' . $e->getMessage());
        }

        if ($response->getStatusCode() != 200) {
            throw new \RuntimeException('Failed to fetch news from New York Times API: ' . $response->getBody());
        }

        $data = json_decode($response->getBody(), true);
        $articles = $data['response']['docs'];

        $news = [];
        foreach ($articles as $article) {
            $news[] = [
                'title' => $article['headline']['main'],
                'description' => $article['abstract'],
                'author' => $article['byline']['original'] ?? 'Unknown',
                'source' => 'New York Times',
                'url' => $article['web_url'],
                'published_at' => $article['pub_date'],
                'category' => $article['news_desk'] ?? $article['section_name'] ?? 'Unknown',
            ];
        }

        return [
            'total' => $data['response']['meta']['hits'],
            'news' => $news
        ];
    }
}
