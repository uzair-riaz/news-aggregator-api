<?php

namespace App\Repositories;

use App\Filters\ArticleQueryBuilder;
use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleRepository
{
    /**
     * Get article by id
     *
     * @param $articleId
     * @return mixed
     */
    public function getById($articleId)
    {
        return Article::find($articleId);
    }

    /**
     * Filter and paginate articles
     *
     * @param $filters
     * @param $page
     * @param $limit
     * @return array|LengthAwarePaginator
     */
    public function filter($filters, $page, $limit)
    {
        $query = new ArticleQueryBuilder();
        if (isset($filters['keyword'])) {
            $query->keyword($filters['keyword']);
        }
        if (isset($filters['date'])) {
            $query->date($filters['date']);
        }
        if (isset($filters['category_id'])) {
            $query->categoryId($filters['category_id']);
        }
        if (isset($filters['source_id'])) {
            $query->sourceId($filters['source_id']);
        }
        $query->with(['source', 'category', 'author']);

        return $query->getQuery()->paginate(perPage: $limit, page: $page)->withQueryString();
    }
}
