<?php
namespace App\Services;

use App\Models\Project;

class ProjectService
{
    public function all()
    {
        return Project::with(['creator', 'tasks'])->latest('updated_at')->get();
    }

    public function store(array $data)
    {
        $data['created_by'] = auth()->id();
        return Project::create($data);
    }
}
