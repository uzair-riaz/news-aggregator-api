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
     * Get an authenticated user's preferences
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $preferences = Auth::user()->preferences()->first();

        return response()->json([
            'preferences' => UserPreferenceResource::make($preferences)
        ], Response::HTTP_OK);
    }

    /**
     * Save a user's preferences
     *
     * @param SaveUserPreferenceRequest $request
     * @return \Illuminate\Http\JsonResponse
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
     * Generate personalized news feed based on user's preferences
     *
     * @return \Illuminate\Http\JsonResponse
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
