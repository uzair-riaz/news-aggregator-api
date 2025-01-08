<?php

namespace App\Services;

use App\Filters\ArticleQueryBuilder;
use App\Repositories\ArticleRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleService
{
    protected ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * Get single article by id
     *
     * @param $articleId
     * @return mixed
     */
    public function getById($articleId)
    {
        return $this->articleRepository->getById($articleId);
    }

    /**
     * Filter articles and apply pagination
     *
     * @param null $filters
     * @param int $page
     * @param int $limit
     * @return array|LengthAwarePaginator
     */
    public function filter($filters = null, $page = 1, $limit = 10)
    {
        return $this->articleRepository->filter($filters, $page, $limit);
    }
}
