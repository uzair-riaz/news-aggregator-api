<?php

namespace App\Repositories;

use App\Models\UserPreference;

class UserPreferenceRepository
{
    /**
     * Save user preference
     *
     * @param $userId
     * @param $preferences
     * @return mixed
     */
    public function save($userId, $preferences)
    {
        return UserPreference::updateOrCreate(
            ['user_id' => $userId],
            ['preferences' => $preferences]
        );
    }
}
