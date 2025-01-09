<?php

namespace App\NewsSources;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class TheGuardian implements NewsSource
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
        $client = new Client(['base_uri' => 'https://content.guardianapis.com/']);

        try {
            $response = $client->get('search', [
                RequestOptions::QUERY => [
                    'api-key' => $this->apiKey,
                    'q' => null,
                    'from-date' => Carbon::yesterday()->toDateString(),
                    'to-date' => Carbon::today()->toDateString(),
                    'order-by' => 'relevance',
                    'page-size' => $this->pageSize,
                    'page' => $page,
                    'show-fields' => 'byline,trailText'
                ]
            ]);
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Failed to fetch news from The Guardian API: ' . $e->getMessage());
        }

        if ($response->getStatusCode() != 200) {
            throw new \RuntimeException('Failed to fetch news from The Guardian API: ' . $response->getBody());
        }

        $data = json_decode($response->getBody(), true);
        $articles = $data['response']['results'];

        $news = [];
        foreach ($articles as $article) {
            $news[] = [
                'title' => $article['webTitle'],
                'source' => 'The Guardian',
                'author' => $article['fields']['byline'] ?? 'Unknown',
                'description' => $article['fields']['trailText'],
                'url' => $article['webUrl'],
                'published_at' => $article['webPublicationDate'],
                'category' => $article['sectionName'] ?? 'Unknown',
            ];
        }

        return [
            'total' => $data['response']['total'],
            'news' => $news
        ];
    }
}
