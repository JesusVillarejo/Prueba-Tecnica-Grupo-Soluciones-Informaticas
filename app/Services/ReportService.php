<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Devuelve los arrays para los selects del formulario de filtros.
     *
     * @return array{
     *   projectsOptions: array<int|string,string>,
     *   usersOptions:    array<int|string,string>
     * }
     */
    public function getFiltersOptions(): array
    {
        $projectsOptions = Project::orderBy('name')
            ->pluck('name', 'id')
            ->prepend('Todos los proyectos', '')
            ->toArray();

        $usersOptions = User::orderBy('name')
            ->pluck('name', 'id')
            ->prepend('Todos los usuarios', '')
            ->toArray();

        return compact('projectsOptions', 'usersOptions');
    }

    /**
     * Genera y devuelve la instancia de DomPDF cargada con la vista PDF.
     *
     * @param  array  $filters
     * @return \Barryvdh\DomPDF\PDF
     */
    public function makePdf(array $filters)
    {
        $query = Task::with(['project','user'])
            ->when(! empty($filters['date_from']), fn($q) => $q->where('start', '>=', $filters['date_from'].' 00:00:00'))
            ->when(! empty($filters['date_to']),   fn($q) => $q->where('end',   '<=', $filters['date_to']  .' 23:59:59'))
            ->when(! empty($filters['project_id']), fn($q, $id)=> $q->where('project_id', $id))
            ->when(! empty($filters['user_id']),    fn($q, $id)=> $q->where('user_id', $id))
            ->orderBy('project_id')
            ->orderBy('start');

        $tasksByProject = $query->get()->groupBy(fn($t) => $t->project->name);

        $totals = $tasksByProject->mapWithKeys(function (Collection $group, string $projectName) {
            $mins = $group->sum(fn($t) => $t->end->diffInMinutes($t->start));
            return [ $projectName => $mins ];
        })->all();

        $projects = Project::orderBy('name')->pluck('name','id');
        $users    = User::orderBy('name')->pluck('name','id');

        $pdf = Pdf::loadView('reports.tasks.pdf', [
            'tasksByProject'   => $tasksByProject,
            'totals'           => $totals,
            'filters'          => $filters,
            'projects'         => $projects,
            'users'            => $users,
        ]);

        $pdf->setPaper('a4','landscape');

        return $pdf;
    }
}
