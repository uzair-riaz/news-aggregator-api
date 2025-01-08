<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user
     *
     * @param $name
     * @param $email
     * @param $password
     * @return mixed
     */
    public function register($name, $email, $password)
    {
        $user = $this->userRepository->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password)
        ]);

        return $user->createToken('token')->plainTextToken;
    }

    /**
     * Log in an existing user
     *
     * @param $email
     * @param $password
     * @return null
     */
    public function login($email, $password)
    {
        $user = $this->userRepository->getByEmail($email);
        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        return $user->createToken('token')->plainTextToken;
    }

    /**
     * Log a user out by deleting all their active tokens
     *
     * @param $user
     * @return mixed
     */
    public function logout($user)
    {
        return $user->tokens()->delete();
    }

    public function resetPassword($email, $password)
    {
        return $this->userRepository->updateByEmail(
            $email,
            ['password' => Hash::make($password)]
        );
    }
}
