<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthorProfileResource extends JsonResource
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
            'user_id' => $this->user_id,
            'url' => $this->url,
            'full_name' => $this->full_name,
            'display_name' => $this->display_name,
            'image' => $this->image,
            'social_links' => $this->social_links,
            'biography' => $this->biography,
            'verified_at' => $this->verified_at,
        ];

        return parent::toArray($request);
    }
}
