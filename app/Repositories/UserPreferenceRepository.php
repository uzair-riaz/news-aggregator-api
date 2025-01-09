<?php

namespace App\Repositories;

use App\Models\UserPreference;

class UserPreferenceRepository
{
    public function save($userId, $preferences)
    {
        return UserPreference::updateOrCreate(
            ['user_id' => $userId],
            ['preferences' => $preferences]
        );
    }
}
