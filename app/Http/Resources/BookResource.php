<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Book
 */
class BookResource extends JsonResource
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
            'designation' => $this->designation,
            'auteur' => $this->auteur,
            'description' => $this->description,
            'editeur' => $this->editeur,
            'annee' => $this->annee?->toDateString(),
            'prix' => $this->prix,
            'cover_url' => $this->cover_url,
            'type' => $this->whenLoaded('type', fn () => ['id' => $this->type->id, 'name' => $this->type->name]),
            'language' => $this->whenLoaded('language', fn () => $this->language
                ? ['id' => $this->language->id, 'name' => $this->language->name]
                : null),
            'categories' => $this->whenLoaded('categories', fn () => $this->categories
                ->map(fn ($category) => ['id' => $category->id, 'name' => $category->name])),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
