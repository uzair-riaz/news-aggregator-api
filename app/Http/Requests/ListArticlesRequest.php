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
            'filters.category_ids' => ['nullable', 'array'],
            'filters.category_ids.*' => ['nullable', 'integer', 'exists:categories,id'],
            'filters.source_ids' => ['nullable', 'array'],
            'filters.source_ids.*' => ['nullable', 'integer', 'exists:sources,id'],

            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100']
        ];
    }
}
