<?php

namespace App\Http\Requests\Attendance;

use GlobalXtreme\Validation\Support\FormRequest;

class CorrectionRequest extends FormRequest
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
            'date' => 'required|date',
            'notes' => 'required|string',
            'clockIn' => 'required|date_format:H:i',
            'clockOut' => 'required|date_format:H:i',
            'timesheetId' => 'required|integer'
        ];
    }
}
