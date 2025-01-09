<?php

namespace App\Repositories;

use App\Models\UserPreference;

class UserPreferenceRepository implements Repository
{
    /**
     * Create or update user preference
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return UserPreference::updateOrCreate(
            ['user_id' => $data['user_id']],
            ['preferences' => $data['preferences']]
        );
    }
}
