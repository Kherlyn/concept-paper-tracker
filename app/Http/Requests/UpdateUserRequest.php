<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return $this->user()->hasRole('admin');
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    $userId = $this->route('user')->id;

    return [
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
      'password' => ['nullable', Password::defaults()],
      'role' => ['required', Rule::in(['requisitioner', 'sps', 'vp_acad', 'auditor', 'accounting', 'admin'])],
      'department' => ['nullable', 'string', 'max:255'],
      'school_year' => ['nullable', 'string', 'max:50'],
      'student_number' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($userId)],
      'is_active' => ['boolean'],
    ];
  }

  /**
   * Get custom messages for validator errors.
   *
   * @return array<string, string>
   */
  public function messages(): array
  {
    return [
      'name.required' => 'The name field is required.',
      'name.max' => 'The name may not be greater than 255 characters.',
      'email.required' => 'The email field is required.',
      'email.email' => 'Please provide a valid email address.',
      'email.unique' => 'This email address is already registered.',
      'role.required' => 'Please select a user role.',
      'role.in' => 'The selected role is invalid.',
      'department.max' => 'The department may not be greater than 255 characters.',
      'school_year.max' => 'The school year may not be greater than 50 characters.',
      'student_number.max' => 'The student number may not be greater than 50 characters.',
      'student_number.unique' => 'This student number is already registered.',
      'is_active.boolean' => 'The active status must be true or false.',
    ];
  }
}
