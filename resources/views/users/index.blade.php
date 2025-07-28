{{-- resources/views/users/index.blade.php --}}
@extends('adminlte::page')

@section('plugins.Datatables', true)

@section('title', 'Gestión de Usuarios')

@section('content_header')
    <h1><i class="fas fa-users"></i> Gestión de Usuarios</h1>
@stop

@section('content')
    <x-adminlte-card title="Usuarios registrados" theme="primary" icon="fas fa-users" collapsible maximizable>
        {{-- Botón Nuevo Usuario --}}
        <x-slot name="toolsSlot">
            <button id="btnNuevo" class="btn btn-success">
                <i class="fas fa-plus"></i> Nuevo Usuario
            </button>
        </x-slot>

        {{-- Tabla --}}
        <table id="tablaUsuarios" class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Creado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
    </x-adminlte-card>

    {{-- Modal Alta / Edición de Usuario --}}
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formUsuario">
                @csrf
                <input type="hidden" id="user_id" name="user_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUsuarioLabel">Nuevo Usuario</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="userErrors" class="alert alert-danger d-none"></div>

                        <div class="form-group">
                            <label for="name">Nombre</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Correo</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="role">Rol</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="user">Usuario</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>

                        <div class="form-group password-group">
                            <label for="password">Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="Dejar vacío si no cambia">
                        </div>

                        <div class="form-group password-group">
                            <label for="password_confirmation">Confirmar contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control" placeholder="Dejar vacío si no cambia">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(function() {
            let tabla = $('#tablaUsuarios').DataTable({
                ajax: '{{ route('users.list') }}',
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'role'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(u) {
                            return `
            <button class="btn btn-sm btn-warning btnEdit" data-id="${u.id}">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-danger btnDelete" data-id="${u.id}">
              <i class="fas fa-trash"></i>
            </button>`;
                        }
                    }
                ]
            });

            // Botón Nuevo
            $('#btnNuevo').click(function() {
                $('#formUsuario')[0].reset();
                $('#user_id').val('');
                $('#userErrors').addClass('d-none').empty();
                $('.password-group').show();
                $('#modalUsuarioLabel').text('Nuevo Usuario');
                $('#modalUsuario').modal('show');
            });

            // Editar
            $('#tablaUsuarios').on('click', '.btnEdit', function() {
                let id = $(this).data('id');
                $.get('{{ route('users.list') }}', function(res) {
                    let u = res.data.find(x => x.id == id);
                    if (!u) return alert('Usuario no encontrado.');
                    $('#user_id').val(u.id);
                    $('#name').val(u.name);
                    $('#email').val(u.email);
                    $('#role').val(u.role);
                    $('#password, #password_confirmation').val('');
                    $('#userErrors').addClass('d-none').empty();
                    $('.password-group').hide();
                    $('#modalUsuarioLabel').text('Editar Usuario');
                    $('#modalUsuario').modal('show');
                });
            });

            // Guardar (crear / actualizar)
            $('#formUsuario').submit(function(e) {
                e.preventDefault();
                let id = $('#user_id').val();
                let url = id ? `/users/${id}` : `/users`;
                let method = id ? 'PUT' : 'POST';
                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    success() {
                        $('#modalUsuario').modal('hide');
                        tabla.ajax.reload();
                    },
                    error(xhr) {
                        let errs = xhr.responseJSON.errors || {};
                        let html = Object.values(errs).flat()
                            .map(m => `<div>${m}</div>`).join('');
                        $('#userErrors').removeClass('d-none').html(html);
                    }
                });
            });

            // Borrar
            $('#tablaUsuarios').on('click', '.btnDelete', function() {
                if (!confirm('¿Eliminar este usuario?')) return;
                let id = $(this).data('id');
                $.ajax({
                    url: `/users/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success() {
                        tabla.ajax.reload();
                    }
                });
            });

        });
    </script>
@stop
