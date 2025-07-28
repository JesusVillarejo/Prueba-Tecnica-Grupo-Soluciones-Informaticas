<?php
namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function __construct(private TaskService $service)
    {
    }

    public function index(): AnonymousResourceCollection
    {
        $userId    = request('user_id', auth()->id());
        $projectId = request('project_id');
        $tasks     = $this->service->allByUser($userId, $projectId);

        return TaskResource::collection($tasks);
    }

    public function store(TaskRequest $request): JsonResponse
    {
        $task = $this->service->store($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Tarea creada correctamente',
            'data'    => new TaskResource($task),
        ], 201);
    }

    public function update(TaskRequest $request, int $id): JsonResponse
    {
        $task = $this->service->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Tarea actualizada correctamente',
            'data'    => new TaskResource($task),
        ]);
    }

    public function destroy(int $id): Response
    {
        $this->service->destroy($id);
        return response()->noContent();
    }
}
