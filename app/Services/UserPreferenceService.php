<?php

namespace App\Services;

use App\Repositories\UserPreferenceRepository;
use App\Repositories\UserRepository;

class UserPreferenceService
{
    protected UserRepository $userRepository;
    protected UserPreferenceRepository $userPreferenceRepository;

    public function __construct(UserRepository $userRepository, UserPreferenceRepository $userPreferenceRepository)
    {
        $this->userRepository = $userRepository;
        $this->userPreferenceRepository = $userPreferenceRepository;
    }

    /**
     * Save a user's preferences
     *
     * @param $userId
     * @param $preferences
     * @return mixed
     */
    public function savePreferences($userId, $preferences)
    {
        return $this->userPreferenceRepository->save($userId, $preferences);
    }
}
