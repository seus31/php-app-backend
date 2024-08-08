<?php

namespace App\Http\Resources\Api\V1;

class CategoryResourceCollection extends ApiResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'data' => CategoryResource::collection($this->collection),
        ];
    }
}

