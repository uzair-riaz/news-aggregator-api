<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * Create a new user
     *
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        return User::firstOrCreate($attributes);
    }

    /**
     * Update user by email
     *
     * @param $email
     * @param array $attributes
     * @return mixed
     */
    public function updateByEmail($email, array $attributes)
    {
        return User::where('email', $email)->update($attributes);
    }

    /**
     * Get user by email
     *
     * @param $email
     * @return mixed
     */
    public function getByEmail($email)
    {
        return User::where('email', $email)->first();
    }
}
