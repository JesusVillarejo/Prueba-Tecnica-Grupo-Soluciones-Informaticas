<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;

class ProjectController extends Controller
{
    protected $service;

    public function __construct(ProjectService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        // Para el select2 del modal de informe
        $projectsOptions = Project::orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $usersOptions = User::orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        return view('projects.index', compact('projectsOptions', 'usersOptions'));
    }

    public function list()
    {
        $projects = $this->service->all();
        return response()->json([
            'data' => ProjectResource::collection($projects),
        ]);
    }

    public function store(ProjectRequest $request)
    {
        $this->service->store($request->validated());
        return response()->json(['message' => 'Proyecto creado correctamente.']);
    }
}
