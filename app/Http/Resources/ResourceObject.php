<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResourceObject extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $req v1uest
     * @return array
     */
    public function toArray($request)
    {
        return [
                'type' => $this->resource->type,
                'id' =>  (string) $this->resource->getRouteKey(),
                'atribures' => $this->resource->fields(),
                'links' => [
                    'self' => route('api.v1.'.$this->resource->type.'.show', $this->resource)
                ]
        ];
    }
}
