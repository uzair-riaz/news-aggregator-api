<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveUserPreferenceRequest;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\UserPreferenceResource;
use App\Services\ArticleService;
use App\Services\UserPreferenceService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{
    protected ArticleService $articleService;
    protected UserPreferenceService $userPreferenceService;

    public function __construct(ArticleService $articleService, UserPreferenceService $userPreferenceService)
    {
        $this->articleService = $articleService;
        $this->userPreferenceService = $userPreferenceService;
    }

    /**
     * @OA\Get(
     *     path="/api/preferences",
     *     summary="Get user preferences",
     *     description="Fetch the preferences of the currently authenticated user.",
     *     operationId="getUserPreferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User preferences retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="preferences",
     *                 type="object",
     *                 @OA\Property(
     *                     property="source_ids",
     *                     type="array",
     *                     description="IDs of the preferred sources",
     *                     @OA\Items(type="integer", example=1)
     *                 ),
     *                 @OA\Property(
     *                     property="category_ids",
     *                     type="array",
     *                     description="IDs of the preferred categories",
     *                     @OA\Items(type="integer", example=2)
     *                 ),
     *                 @OA\Property(
     *                     property="author_ids",
     *                     type="array",
     *                     description="IDs of the preferred authors",
     *                     @OA\Items(type="integer", example=3)
     *                 )
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
    public function index()
    {
        $preferences = Auth::user()->preferences()->first();

        return response()->json([
            'preferences' => UserPreferenceResource::make($preferences)
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/user/personalized-feed",
     *     summary="Save user preferences",
     *     description="Save or update the preferences of the currently authenticated user.",
     *     operationId="saveUserPreferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="preferences",
     *                 type="object",
     *                 description="The preferences data to be saved",
     *                 @OA\Property(
     *                     property="source_ids",
     *                     type="array",
     *                     description="IDs of the preferred sources",
     *                     @OA\Items(type="integer", example=1)
     *                 ),
     *                 @OA\Property(
     *                     property="category_ids",
     *                     type="array",
     *                     description="IDs of the preferred categories",
     *                     @OA\Items(type="integer", example=2)
     *                 ),
     *                 @OA\Property(
     *                     property="author_ids",
     *                     type="array",
     *                     description="IDs of the preferred authors",
     *                     @OA\Items(type="integer", example=3)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Preferences saved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Preferences saved")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(property="errors", type="object", description="Details about validation errors")
     *         )
     *     )
     * )
     */
    public function save(SaveUserPreferenceRequest $request)
    {
        $user = Auth::user();
        $this->userPreferenceService->savePreferences($user->id, $request->get('preferences'));

        return response()->json([
            "message" => "Preferences saved"
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/personalized-news-feed",
     *     summary="Generate personalized news feed",
     *     description="Generates a personalized news feed based on the authenticated user's preferences.",
     *     operationId="getPersonalizedNewsFeed",
     *     tags={"News Feed"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Personalized news feed retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="articles",
     *                 type="object",
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1, description="The ID of the article"),
     *                         @OA\Property(property="title", type="string", example="Example Title", description="The title of the article"),
     *                         @OA\Property(property="url", type="string", example="https://www.example.com", description="The link to the article"),
     *                         @OA\Property(property="description", type="string", example="This is a sample article description", description="The description of the article"),
     *                         @OA\Property(
     *                             property="source",
     *                             type="object",
     *                             description="Details of the article's source",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Tech Blog")
     *                         ),
     *                         @OA\Property(
     *                             property="category",
     *                             type="object",
     *                             description="Details of the article's category",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Technology")
     *                         ),
     *                         @OA\Property(
     *                             property="author",
     *                             type="object",
     *                             description="Details of the article's author",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="John Doe")
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(property="meta", type="object", description="Pagination metadata"),
     *                 @OA\Property(property="links", type="object", description="Pagination links")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Preferences not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Preferences not found, cannot generate personalized news feed.")
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
    public function personalizedNewsFeed()
    {
        $userPreferences = Auth::user()->preferences()->first();
        $preferences = $userPreferences['preferences'];
        if (!$preferences) {
            return response()->json([
                "message" => "Preferences not found, cannot generate personalized news feed."
            ], Response::HTTP_NOT_FOUND);
        }

        $filters = [];
        if (isset($preferences['source_ids'])) {
            $filters['source_ids'] = $preferences['source_ids'];
        }
        if (isset($preferences['category_ids'])) {
            $filters['category_ids'] = $preferences['category_ids'];
        }
        if (isset($preferences['author_ids'])) {
            $filters['author_ids'] = $preferences['author_ids'];
        }
        $articles = $this->articleService->filter($filters);
        return response()->json([
            'articles' => ArticleResource::collection($articles)->response()->getData(true)
        ], Response::HTTP_OK);
    }
}
