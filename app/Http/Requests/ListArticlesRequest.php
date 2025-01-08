<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListArticlesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'filters' => ['nullable', 'array'],
            'filters.keyword' => ['nullable', 'string'],
            'filters.date' => ['nullable', 'date'],
            'filters.category' => ['nullable', 'string'],
            'filters.source' => ['nullable', 'string'],

            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100']
        ];
    }
}
