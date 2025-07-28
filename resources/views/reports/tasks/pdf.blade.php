{{-- resources/views/reports/tasks/pdf.blade.php --}}
@php use Carbon\Carbon; @endphp
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 0; }
    .header-table { width: 100%; border-bottom: 2px solid #333; margin-bottom: 15px; }
    .header-table td { vertical-align: middle; }
    .logo-cell { width: 30%; padding: 5px; }
    .logo-cell img { max-height: 60px; }
    .info-cell { width: 70%; text-align: right; padding: 5px; font-size: 11px; }
    .info-cell .label { font-weight: bold; }
    h2.title { text-align: center; margin: 10px 0; font-size: 16px; }
    .project-title { background: #00539F; color: white; padding: 4px; margin-top: 10px; font-size: 14px; }
    table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    table.data-table th,
    table.data-table td { border: 1px solid #333; padding: 4px; font-size: 11px; }
    table.data-table th { background: #F0F0F0; }
    .total-row td { border-top: 2px solid #333; font-weight: bold; text-align: right; }
  </style>
</head>
<body>
  <table class="header-table">
    <tr>
      <td class="logo-cell">
        <img src="{{ public_path('img/logo.png') }}" alt="Logo Empresa">
      </td>
      <td class="info-cell">
        <div>
          <span class="label">Desde fecha:</span>
          @if(!empty($filters['date_from']))
            {{ Carbon::parse($filters['date_from'])->format('d-m-Y') }}
          @else
            —
          @endif
        </div>
        <div>
          <span class="label">Hasta fecha:</span>
          @if(!empty($filters['date_to']))
            {{ Carbon::parse($filters['date_to'])->format('d-m-Y') }}
          @else
            —
          @endif
        </div>
        <div>
          <span class="label">Proyecto:</span>
          {{ $filters['project_id']
               ? ($projects[$filters['project_id']] ?? '—')
               : 'Todos' }}
        </div>
        <div>
          <span class="label">Usuario:</span>
          {{ $filters['user_id']
               ? ($users[$filters['user_id']] ?? '—')
               : 'Todos' }}
        </div>
      </td>
    </tr>
  </table>

  <h2 class="title">Informe de Tareas Realizadas</h2>

  @foreach($tasksByProject as $projectName => $tasks)
    <div class="project-title">{{ $projectName }}</div>

    <table class="data-table">
      <thead>
        <tr>
          <th style="width:5%">ID</th>
          <th style="width:20%">Inicio</th>
          <th style="width:20%">Fin</th>
          <th style="width:10%">Minutos</th>
          <th style="width:20%">Usuario</th>
          <th style="width:25%">Descripción</th>
        </tr>
      </thead>
      <tbody>
        @foreach($tasks as $t)
          @php
            $mins = $t->start->diffInMinutes($t->end);
          @endphp
          <tr>
            <td>{{ $t->id }}</td>
            <td>{{ $t->start->format('d-m-Y H:i') }}</td>
            <td>{{ $t->end->format('d-m-Y H:i') }}</td>
            <td style="text-align: right">{{ $mins }}</td>
            <td>{{ $t->user->name }}</td>
            <td>{{ $t->description }}</td>
          </tr>
        @endforeach
        <tr class="total-row">
          <td colspan="3">Total minutos:</td>
          <td colspan="3">
            {{
              collect($tasks)
                ->sum(fn($t) => $t->start->diffInMinutes($t->end))
            }}
          </td>
        </tr>
      </tbody>
    </table>
  @endforeach
</body>
</html>
