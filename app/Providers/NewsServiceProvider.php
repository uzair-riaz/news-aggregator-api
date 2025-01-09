<?php

namespace App\Providers;

use App\NewsSources\GoogleNews;
use App\NewsSources\NewsCatcher;
use App\NewsSources\NewsData;
use App\NewsAggregator;
use App\NewsSources\NewYorkTimes;
use App\NewsSources\TheGuardian;
use App\Repositories\ArticleRepository;
use App\Repositories\AuthorRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\SourceRepository;
use Illuminate\Support\ServiceProvider;

class NewsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(NewsAggregator::class, function ($app) {
            return new NewsAggregator(
                [
                    new GoogleNews(config('news.google_news_api_key'), config('news.page_size')),
                    new TheGuardian(config('news.the_guardian_api_key'), config('news.page_size')),
                    new NewYorkTimes(config('news.new_york_times_api_key'), config('news.page_size'))
                ],
                new ArticleRepository(),
                new AuthorRepository(),
                new CategoryRepository(),
                new SourceRepository()
            );
        });
    }
}
