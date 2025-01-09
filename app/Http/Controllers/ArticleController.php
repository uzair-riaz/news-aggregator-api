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
     * @OA\Get(
     *     path="/api/articles",
     *     summary="List articles with optional filters",
     *     description="Fetch a paginated list of articles with optional filters. Requires authentication.",
     *     operationId="listArticles",
     *     tags={"Articles"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="filters",
     *         in="query",
     *         required=false,
     *         description="Filter criteria for articles (e.g., by keyword, date, categories and sources)",
     *         @OA\Schema(type="string", example="filters[keyword]=tech&filters[date]=2025-01-01*filters[category_ids][0]=1&filters[source_ids][0]=1")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="The page number for pagination",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         description="The number of articles per page",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of articles",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="articles",
     *                 type="object",
     *                 @OA\Property(
     *                     property="article",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1, description="The ID of the article"),
     *                     @OA\Property(property="title", type="string", example="Example Title", description="The title of the article"),
     *                     @OA\Property(property="url", type="string", example="https://www.example.com", description="The link to the article"),
     *                     @OA\Property(property="description", type="string", example="https://www.example.com", description="The descritpion of the article")
     *                 ),
     *                 @OA\Property(property="meta", type="object", description="Pagination metadata"),
     *                 @OA\Property(property="links", type="object", description="Pagination links")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function index(ListArticlesRequest $request)
    {
        $articles = $this->articleService->filter(...$request->only(['filters', 'page', 'limit']));

        return response()->json([
            'articles' => ArticleResource::collection($articles)->response()->getData(true)
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     summary="Get article details",
     *     description="Fetch details of a specific article by its ID, including related source, category, and author information.",
     *     operationId="getArticle",
     *     tags={"Articles"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the article",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1, description="The ID of the article"),
     *             @OA\Property(property="title", type="string", example="Example Title", description="The title of the article"),
     *             @OA\Property(property="url", type="string", example="https://www.example.com", description="The link to the article"),
     *             @OA\Property(property="description", type="string", example="https://www.example.com", description="The descritpion of the article"),
     *             @OA\Property(
     *                 property="source",
     *                 type="object",
     *                 description="Details of the article's source",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Tech Blog")
     *             ),
     *             @OA\Property(
     *                 property="category",
     *                 type="object",
     *                 description="Details of the article's category",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Technology")
     *             ),
     *             @OA\Property(
     *                 property="author",
     *                 type="object",
     *                 description="Details of the article's author",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Article not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function show(Article $article)
    {
        return response()->json([
            'article' => new ArticleResource($article)
        ], Response::HTTP_OK);
    }
}
