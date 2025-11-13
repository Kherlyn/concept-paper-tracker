<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
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
    return [
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
      'password' => ['required', Password::defaults()],
      'role' => ['required', Rule::in(['requisitioner', 'sps', 'vp_acad', 'auditor', 'accounting', 'admin'])],
      'department' => ['nullable', 'string', 'max:255'],
      'school_year' => ['nullable', 'string', 'max:50'],
      'student_number' => ['nullable', 'string', 'max:50', 'unique:users'],
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
      'password.required' => 'The password field is required.',
      'role.required' => 'Please select a user role.',
      'role.in' => 'The selected role is invalid.',
      'department.max' => 'The department may not be greater than 255 characters.',
      'school_year.max' => 'The school year may not be greater than 50 characters.',
      'student_number.max' => 'The student number may not be greater than 50 characters.',
      'student_number.unique' => 'This student number is already registered.',
      'is_active.boolean' => 'The active status must be true or false.',
    ];
  }

  /**
   * Prepare the data for validation.
   */
  protected function prepareForValidation(): void
  {
    // Ensure is_active defaults to true if not provided
    if (!$this->has('is_active')) {
      $this->merge([
        'is_active' => true,
      ]);
    }
  }
}
