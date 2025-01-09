<?php

namespace App\NewsSources;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class GoogleNews implements NewsSource
{
    protected $apiKey;
    protected $pageSize;

    public function __construct($apiKey, $pageSize)
    {
        $this->apiKey = $apiKey;
        $this->pageSize = $pageSize;
    }

    /**
     * Fetch news from Google News sources
     *
     * @param int $page
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchNews(int $page = 1)
    {
        $client = new Client([
            'base_uri' => 'https://newsapi.org/v2/',
            'timeout'  => 2.0,
        ]);

        try {
            $response = $client->request('GET', 'everything', [
                RequestOptions::QUERY => [
                    'apiKey' => $this->apiKey,
                    'page' => $page,
                    'pageSize' => $this->pageSize,
                    'q' => 'Germany'
                ]
            ]);
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Failed to fetch news from GoogleNews API: ' . $e->getMessage());
        }

        if ($response->getStatusCode() != 200) {
            throw new \RuntimeException('Failed to fetch news from GoogleNews API: ' . $response->getBody());
        }

        $data = json_decode($response->getBody(), true);
        $articles = $data['articles'];

        $news = [];
        foreach ($articles as $article) {
            $news[] = [
                'title' => $article['title'],
                'description' => $article['description'],
                'content' => $article['content'],
                'author' => $article['author'] ?? 'Unknown',
                'url' => $article['url'],
                'source' => $article['source']['name'],
                'published_at' => Carbon::parse($article['publishedAt'])->toDateTimeString(),
                'category' => 'Unknown'
            ];
        }

        return [
            'total' => $data['totalResults'],
            'news' => $news
        ];
    }
}
