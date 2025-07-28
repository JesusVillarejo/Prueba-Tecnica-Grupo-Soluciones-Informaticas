<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportService;

class ReportController extends Controller
{
    public function __construct(private ReportService $service) {}


    /** Generar y stream del PDF */
    public function pdf(Request $request)
    {
        $filters = $request->validate([
            'date_from'  => 'nullable|date',
            'date_to'    => 'nullable|date|after_or_equal:date_from',
            'project_id' => 'nullable|exists:projects,id',
            'user_id'    => 'nullable|exists:users,id',
        ]);

        return $this
            ->service
            ->makePdf($filters)
            ->stream('informe_tareas.pdf');
    }
}
