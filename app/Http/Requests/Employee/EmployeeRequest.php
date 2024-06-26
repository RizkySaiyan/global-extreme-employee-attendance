<?php

namespace App\Http\Requests\Employee;

use GlobalXtreme\Validation\Support\FormRequest;

class EmployeeRequest extends FormRequest
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
            'name' => 'required|string',
            'companyOfficeId' => 'required|exists:component_company_offices,id',
            'departmentId' => 'required|exists:component_departments,id',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'photo' => 'required|mimes:jpg,png',
            'email' => 'required|email',
            'password' => 'required|string',
            'fatherName' => 'required|string',
            'fatherPhone' => 'nullable|string',
            'fatherEmail' => 'nullable|string',
            'motherName' => 'required|string',
            'motherPhone' => 'nullable|string',
            'motherEmail' => 'nullable|string',
            'siblings.*.name' => 'required|string',
            'siblings.*.phone' => 'nullable|string',
            'siblings.*.email' => 'nullable|string',
        ];
    }
}
