<?php

namespace App\Filters;

use App\Models\Article;
use Carbon\Carbon;

class ArticleQueryBuilder
{
    public $query;

    public function __construct()
    {
        $this->query = Article::query();
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function with($relations)
    {
        $this->query = $this->query->with($relations);
    }

    /**
     * Apply general keyword filter
     *
     * @param $keyword
     * @return void
     */
    public function keyword($keyword)
    {
        $this->query = $this->query->where(function ($query) use ($keyword) {
            $query->where('title', 'like', '%' . $keyword . '%')
                ->orWhere('description', 'like', '%' . $keyword . '%');
        });
    }

    /**
     * Apply date filter
     *
     * @param $date
     * @return void
     */
    public function date($date)
    {
        $date = Carbon::parse($date)->toDateString();
        $this->query = $this->query->whereDate('published_at', $date);
    }

    /**
     * Apply category filter
     *
     * @param $categoryIds
     * @return void
     */
    public function categoryIds($categoryIds)
    {
        $this->query = $this->query->whereIn('category_id', $categoryIds);
    }

    /**
     * Apply source filter
     *
     * @param $sourceIds
     * @return void
     */
    public function sourceIds($sourceIds)
    {
        $this->query = $this->query->whereIn('source_id', $sourceIds);
    }

    /**
     * Apply author filter
     *
     * @param $authorIds
     * @return void
     */
    public function authorIds($authorIds)
    {
        $this->query = $this->query->whereIn('author_id', $authorIds);
    }
}
