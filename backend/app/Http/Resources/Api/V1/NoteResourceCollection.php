<?php

namespace App\Http\Resources\Api\V1;

class NoteResourceCollection extends ApiResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'data' => NoteResource::collection($this->collection),
        ];
    }
}

