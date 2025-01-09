<?php

namespace App;

use App\NewsSources\NewsSource;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\SourceRepository;
use GuzzleHttp\Exception\ClientException;
use App\Repositories\AuthorRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NewsAggregator
{
    protected $apis;

    protected ArticleRepository $articleRepository;
    protected AuthorRepository $authorRepository;
    protected CategoryRepository $categoryRepository;
    protected SourceRepository $sourceRepository;
    protected $logger;

    public function __construct(array $apis, ArticleRepository $articleRepository, AuthorRepository $authorRepository, CategoryRepository $categoryRepository, SourceRepository $sourceRepository)
    {
        $this->apis = $apis;
        $this->articleRepository = $articleRepository;
        $this->authorRepository = $authorRepository;
        $this->categoryRepository = $categoryRepository;
        $this->sourceRepository = $sourceRepository;
        $this->logger = Log::channel('news');
    }

    /**
     * Aggregate news from different sources defined in $apis array and save them in local DB
     *
     * @return void
     */
    public function aggregate()
    {
        /** @var NewsSource $api */
        foreach ($this->apis as $api) {
            $this->logger->info('Fetching news from ' . $api::class);
            try {
                $page = 1;
                $pageSize = config('news.page_size');
                try {
                    do {
                        $this->logger->info('Fetching page ' . $page);
                        $data = $api->fetchNews($page);
                        $this->logger->info('Page ' . $page . ' fetching successful');

                        $this->logger->info('Saving page ' . $page);
                        DB::transaction(function () use ($data) {
                            foreach ($data['news'] as $newsItem) {
                                DB::transaction(function () use ($newsItem) {
                                    $author = $this->authorRepository->create(['name' => $newsItem['author']]);
                                    $category = $this->categoryRepository->create(['name' => $newsItem['category']]);
                                    $source = $this->sourceRepository->create(['name' => $newsItem['source']]);

                                    $newsItem['author_id'] = $author->id;
                                    $newsItem['category_id'] = $category->id;
                                    $newsItem['source_id'] = $source->id;

                                    $this->articleRepository->create($newsItem);
                                });
                            }
                        });
                        $this->logger->info('Page ' . $page . ' saved successfully');

                        $page++;
                        $total = $data['total'];
                    } while ($page <= ceil($total / $pageSize));
                } catch (\RuntimeException $e) {
                    $this->logger->error($e->getMessage());
                }
            } catch (ClientException $ex) {
                $this->logger->error($ex->getMessage());
            }
        }
    }
}
