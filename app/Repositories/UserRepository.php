<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function create(array $attributes)
    {
        return User::create($attributes);
    }

    public function updateByEmail($email, array $attributes)
    {
        return User::where('email', $email)->update($attributes);
    }

    public function getByEmail($email)
    {
        return User::where('email', $email)->first();
    }
}
