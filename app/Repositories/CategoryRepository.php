<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository implements Repository
{
    /**
     * Create a new category
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return Category::firstOrCreate($data);
    }
}
