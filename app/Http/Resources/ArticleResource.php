<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'url' => $this->url,
            'description' => $this->description,
            'published_at' => $this->published_at,
            'source' => SourceResource::make($this->whenLoaded('source')),
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'author' => AuthorResource::make($this->whenLoaded('author'))
        ];
    }
}
