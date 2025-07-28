@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
<div class="container">
    <h1 class="mb-4">Mi Perfil</h1>

    <div class="card mb-4">
        <div class="card-header">Información del perfil</div>
        <div class="card-body">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Actualizar contraseña</div>
        <div class="card-body">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Eliminar cuenta</div>
        <div class="card-body">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
