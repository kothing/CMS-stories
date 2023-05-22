<?php

namespace Botble\Blog\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListCategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'children' => CategoryResource::collection($this->children),
            'parent' => new CategoryResource($this->parent),
        ];
    }
}
