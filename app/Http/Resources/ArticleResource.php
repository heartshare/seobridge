<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'title' => $this->title,
            'intro_image' => $this->intro_image,
            'intro_text' => $this->intro_text,
            'full_text' => $this->full_text,
            'author_id' => $this->author_id,
            'category_id' => $this->category_id,
            'published_at' => $this->published_at,
        ];
    }
}
