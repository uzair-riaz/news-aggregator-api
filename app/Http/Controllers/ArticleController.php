<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListArticlesRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    protected ArticleService $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * List articles after filtering and pagination
     *
     * @param ListArticlesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListArticlesRequest $request)
    {
        $articles = $this->articleService->filter(...$request->only(['filters', 'page', 'limit']));

        return response()->json([
            'articles' => ArticleResource::collection($articles)->response()->getData(true)
        ], Response::HTTP_OK);
    }

    /**
     * Show single article details
     *
     * @param Article $article
     * @return ArticleResource
     */
    public function show(Article $article) {
        return ArticleResource::make($article->load('source', 'category', 'author'));
    }
}
