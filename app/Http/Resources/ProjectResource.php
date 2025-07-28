<?php
// App/Http/Resources/ProjectResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'creator'    => $this->creator->name,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'tasks'      => \App\Http\Resources\TaskResource::collection($this->tasks),
        ];
    }
}
