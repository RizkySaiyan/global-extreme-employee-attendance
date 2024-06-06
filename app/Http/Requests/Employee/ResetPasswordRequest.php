<?php

namespace App\Http\Requests\Employee;

use App\Services\Constant\Employee\EmployeeUserRole;
use GlobalXtreme\Validation\Support\FormRequest;
use Illuminate\Validation\Rule;

class ResetPasswordRequest extends FormRequest
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
            'existingPassword' => [
                Rule::excludeIf($this->user()->role == EmployeeUserRole::ADMIN_ID),
                'required',
            ],
            'newPassword' => 'min:8',
            'confirmPassword' => 'required_with:newPassword|same:newPassword|min:8',
        ];
    }
}
