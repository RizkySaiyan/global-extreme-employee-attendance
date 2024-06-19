<?php

namespace App\Http\Requests\Attendance;

use GlobalXtreme\Validation\Support\FormRequest;

class ScheduleRequest extends FormRequest
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
            'employeeId' => 'required|exists:employees,id',
            'type' => 'required|string',
            'reference' => 'required|string',
            'date' => 'required|date'
        ];
    }
}
