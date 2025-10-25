<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'nullable|in:pending,in_progress,completed',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'due_date' => 'nullable|date',
        ];
    }

    /**
     * Get custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la tarea es obligatorio.',
            'name.max' => 'El nombre de la tarea no puede exceder los 255 caracteres.',
            'project_id.required' => 'El proyecto es obligatorio.',
            'project_id.exists' => 'El proyecto seleccionado no existe.',
            'assigned_to.exists' => 'El usuario asignado no existe.',
            'status.in' => 'El estado debe ser uno de: pending, in_progress, completed.',
            'percentage.numeric' => 'El porcentaje debe ser un número.',
            'percentage.min' => 'El porcentaje debe ser mayor o igual a 0.',
            'percentage.max' => 'El porcentaje debe ser menor o igual a 100.',
            'due_date.date' => 'La fecha de vencimiento debe ser una fecha válida.',
        ];
    }
}
