<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->project->name,
            'start'         => $this->start->toIso8601String(),
            'end'           => $this->end->toIso8601String(),
            'extendedProps' => [
                'description' => $this->description,
                'project_id'  => $this->project_id,
                'user_id'     => $this->user_id,
            ],
        ];
    }
}
