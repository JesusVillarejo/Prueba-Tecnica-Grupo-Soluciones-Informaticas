<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'project_id'  => 'required|exists:projects,id',
            'user_id'     => 'required|exists:users,id',
            'start'       => 'required|date',
            'end'         => 'required|date|after_or_equal:start',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.required' => 'Debes indicar el proyecto.',
            'project_id.exists'   => 'El proyecto seleccionado no existe.',
            'user_id.required'    => 'Debes indicar el usuario.',
            'user_id.exists'      => 'El usuario seleccionado no existe.',
            'start.required'      => 'Debes indicar la fecha y hora de inicio.',
            'start.date'          => 'El inicio debe ser una fecha válida.',
            'end.required'        => 'Debes indicar la fecha y hora de fin.',
            'end.date'            => 'El fin debe ser una fecha válida.',
            'end.after_or_equal'  => 'El fin debe ser igual o posterior al inicio.',
            'description.string'  => 'La descripción debe ser texto.',
            'description.max'     => 'La descripción no puede superar los 1000 caracteres.',
        ];
    }
}
