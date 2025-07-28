{{-- resources/views/projects/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Control de Proyectos')
@section('plugins.FullCalendar', true)
@section('plugins.Sweetalert2', true)

@section('css')
    <style>
        .list-group-item.project-item {
            background-color: #ffe680;
            margin-bottom: 5px;
            cursor: grab;
        }
    </style>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@stop

@section('content_header')
    <h1>
        <i class="fas fa-project-diagram"></i> Control de Proyectos
        <select id="selectUser" name="filter_user" class="form-control w-25 float-right ml-2">
            <option value="">— Todos los usuarios —</option>
            @foreach ($usersOptions as $id => $name)
                <option value="{{ $id }}" @if ($id === auth()->id()) selected @endif>{{ $name }}
                </option>
            @endforeach
        </select>
    </h1>
@stop

@section('content')
    <div class="row no-gutters">
        {{-- Panel de Proyectos --}}
        <div class="col-md-4">
            <div class="card m-3">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <strong><i class="fas fa-folder-open"></i> Proyectos</strong>
                    <div>
                        <button id="btnNuevoProyecto" class="btn btn-success btn-sm mr-1" title="Nuevo Proyecto">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button id="btnInformePDF" class="btn btn-primary btn-sm" title="Informe PDF" data-toggle="modal"
                            data-target="#modalReport">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush" id="external-projects"></ul>
                </div>
            </div>
        </div>

        {{-- Calendario --}}
        <div class="col-md-8">
            <div class="card m-3">
                <div class="card-header bg-white">
                    <strong><i class="far fa-calendar-alt"></i> Calendario</strong>
                </div>
                <div class="card-body p-0">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Nuevo Proyecto --}}
    <div class="modal fade" id="modalProyecto" tabindex="-1">
        <div class="modal-dialog">
            <form id="formProyecto">@csrf
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5>Nuevo Proyecto</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input name="name" class="form-control" placeholder="Nombre del proyecto" required>
                        <div class="invalid-feedback" id="errorProyecto"></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button class="btn btn-success">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Nueva Tarea --}}
    <div class="modal fade" id="modalTarea" tabindex="-1">
        <div class="modal-dialog">
            <form id="formTarea">@csrf
                <input type="hidden" id="task_project_id" name="project_id">
                <input type="hidden" id="task_user_id" name="user_id" value="{{ auth()->id() }}">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5>Evento</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Proyecto</label>
                            <input type="text" id="task_project_name" class="form-control" readonly>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Inicio tarea</label>
                                <input type="datetime-local" id="task_start" name="start" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Fin tarea</label>
                                <input type="datetime-local" id="task_end" name="end" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Texto Informativo</label>
                            <textarea id="task_description" name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Informe PDF --}}
    <div class="modal fade" id="modalReport" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="formReport" action="{{ route('reports.tasks.pdf') }}" method="POST" target="_blank">@csrf
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5>Informe de Tareas</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Fecha Desde</label>
                                <input type="text" name="date_from_display" class="form-control datepicker"
                                    placeholder="dd/mm/aaaa">
                                <input type="hidden" name="date_from">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Fecha Hasta</label>
                                <input type="text" name="date_to_display" class="form-control datepicker"
                                    placeholder="dd/mm/aaaa">
                                <input type="hidden" name="date_to">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Proyecto</label>
                                <select name="project_id" class="form-control">
                                    <option value="">— Todos los proyectos —</option>
                                    @foreach ($projectsOptions as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Usuario</label>
                                <select name="user_id" class="form-control">
                                    <option value="">— Todos los usuarios —</option>
                                    @foreach ($usersOptions as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-file-pdf"></i> Generar PDF
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop


@section('js')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        $('.datepicker').datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true
        });

        $('#formReport').on('submit', function(e) {
            const parseDMY = s => {
                const [d, m, y] = s.split('/');
                return `${y}-${m.padStart(2,'0')}-${d.padStart(2,'0')}`;
            };
            const df = $('input[name=date_from_display]').val();
            const dt = $('input[name=date_to_display]').val();
            $('input[name=date_from]').val(parseDMY(df));
            $('input[name=date_to]').val(parseDMY(dt));
        });
        document.addEventListener('DOMContentLoaded', function() {
            $('.datepicker').datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true
            });

            function formatLocal(date) {
                const pad = n => String(n).padStart(2, '0');
                return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()) +
                    'T' + pad(date.getHours()) + ':' + pad(date.getMinutes());
            }

            function loadProjects() {
                const uid = $('#selectUser').val();
                $.get('{{ route('projects.list') }}', {
                    user_id: uid
                }, res => {
                    const list = $('#external-projects').empty();
                    (res.data || []).forEach(p => {
                        list.append(`
          <li class="list-group-item project-item d-flex justify-content-between align-items-center"
              data-id="${p.id}"
              data-title="${p.name}">
            <div>
              <div class="font-weight-bold">${p.name}</div>
              <small class="text-muted">Creado por ${p.creator}</small>
            </div>
            <small class="text-muted">${p.created_at}</small>
          </li>`);
                    });
                    new FullCalendar.Draggable(
                        document.getElementById('external-projects'), {
                            itemSelector: '.project-item',
                            eventData: el => ({
                                title: el.dataset.title,
                                extendedProps: {
                                    project_id: el.dataset.id
                                }
                            })
                        }
                    );
                });
            }

            loadProjects();
            $('#selectUser').on('change', loadProjects);

            $('#btnNuevoProyecto').click(() => {
                $('#formProyecto')[0].reset();
                $('#errorProyecto').hide();
                $('#modalProyecto').modal('show');
            });
            $('#formProyecto').submit(e => {
                e.preventDefault();
                $.post('{{ route('projects.store') }}', $(e.target).serialize())
                    .done(res => {
                        $('#modalProyecto').modal('hide');
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadProjects();
                    })
                    .fail(err => {
                        $('#errorProyecto').text(err.responseJSON.errors.name[0] || 'Error').show();
                    });
            });

            const calendar = new FullCalendar.Calendar(
                document.getElementById('calendar'), {
                    timeZone: 'local',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    initialView: 'timeGridWeek',
                    editable: true,
                    droppable: true,
                    eventResizableFromStart: true,

                    drop(info) {
                        $('#formTarea')[0].reset();
                        const d = new Date(info.date);
                        const isoStart = formatLocal(d);
                        const dEnd = new Date(d.getTime() + 60 * 60000);
                        const isoEnd = formatLocal(dEnd);

                        $('#task_start').val(isoStart);
                        $('#task_end').val(isoEnd);

                        $('#task_project_id').val(info.draggedEl.dataset.id);
                        $('#task_project_name').val(info.draggedEl.dataset.title);
                        $('#modalTarea').modal('show');
                    },



                    eventReceive(info) {
                        info.event.remove();
                    },

                    events(fetchInfo, success, failure) {
                        $.getJSON('{{ route('tasks.index') }}', {
                                user_id: $('#selectUser').val()
                            })
                            .done(data => success(Array.isArray(data) ? data : (data.data || [])))
                            .fail(failure);
                    },

                    eventDrop(info) {
                        let ev = info.event,
                            start = formatLocal(ev.start),
                            end = formatLocal(ev.end || ev.start);

                        $.ajax({
                            url: `/tasks/${ev.id}`,
                            method: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}',
                                project_id: ev.extendedProps.project_id,
                                user_id: $('#selectUser').val(),
                                start,
                                end,
                                description: ev.extendedProps.description || ''
                            }
                        }).done(res => {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            calendar.refetchEvents();
                        }).fail(xhr => {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: xhr.responseJSON?.message || 'Error al mover',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            info.revert();
                        });
                    },

                    eventResize(info) {
                        let ev = info.event,
                            start = formatLocal(ev.start),
                            end = formatLocal(ev.end);

                        $.ajax({
                            url: `/tasks/${ev.id}`,
                            method: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}',
                                project_id: ev.extendedProps.project_id,
                                user_id: $('#selectUser').val(),
                                start,
                                end,
                                description: ev.extendedProps.description || ''
                            }
                        }).done(res => {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            calendar.refetchEvents();
                        }).fail(xhr => {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: xhr.responseJSON?.message || 'Error al redimensionar',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            info.revert();
                        });
                    },

                    eventClick(info) {
                        if (!confirm('¿Eliminar esta tarea?')) return;
                        $.ajax({
                            url: `/tasks/${info.event.id}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            }
                        }).done(res => {
                            info.event.remove();
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }).fail(xhr => {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: xhr.responseJSON?.message || 'Error al eliminar',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        });
                    }
                }
            );
            calendar.render();

            $('#formTarea').submit(e => {
                e.preventDefault();

                const start = $('#task_start').val();
                const end = $('#task_end').val();

                $.post('{{ route('tasks.store') }}', {
                        _token: '{{ csrf_token() }}',
                        project_id: $('#task_project_id').val(),
                        user_id: $('#task_user_id').val(),
                        start,
                        end,
                        description: $('#task_description').val()
                    })
                    .done(res => {
                        $('#modalTarea').modal('hide');
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        calendar.refetchEvents();
                    })
                    .fail(xhr => {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: xhr.responseJSON?.message || 'Error al crear tarea',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    });
            });


        });
    </script>
@stop
