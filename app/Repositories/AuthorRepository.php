<?php

namespace App\Repositories;

use App\Models\Author;

class AuthorRepository implements Repository
{
    /**
     * Create a new author
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return Author::firstOrCreate($data);
    }
}
