<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Collection;

class TaskService
{
    /**
     * Obtener todas las tareas de un usuario,
     * opcionalmente filtradas por proyecto.
     */
    public function allByUser(int $userId, ?int $projectId = null): Collection
    {
        return Task::where('user_id', $userId)
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->orderBy('start')
            ->get();
    }

    /**
     * Crear una nueva tarea.
     */
    public function store(array $data): Task
    {
        return Task::create([
            'project_id'  => $data['project_id'],
            'user_id'     => $data['user_id'],
            'start'       => $data['start'],
            'end'         => $data['end'],
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * Actualizar una tarea existente.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, array $data): Task
    {
        $task = Task::findOrFail($id);

        $task->update([
            'start'       => $data['start']       ?? $task->start,
            'end'         => $data['end']         ?? $task->end,
            'description' => $data['description'] ?? $task->description,
        ]);

        return $task;
    }

    /**
     * Eliminar una tarea.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function destroy(int $id): bool
    {
        $task = Task::findOrFail($id);
        return $task->delete();
    }
}
