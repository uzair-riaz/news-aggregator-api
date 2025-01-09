<?php

namespace App\Repositories;

use App\Models\Source;

class SourceRepository implements Repository
{
    /**
     * Create a new source
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return Source::firstOrCreate($data);
    }
}
