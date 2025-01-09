<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveUserPreferenceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'preferences' => ['required', 'array'],
            'preferences.source_ids' => ['nullable', 'array'],
            'preferences.source_ids.*' => ['required', 'integer', 'exists:sources,id'],
            'preferences.category_ids' => ['nullable', 'array'],
            'preferences.category_ids.*' => ['required', 'integer', 'exists:categories,id'],
            'preferences.author_ids' => ['nullable', 'array'],
            'preferences.author_ids.*' => ['required', 'integer', 'exists:authors,id']
        ];
    }
}
