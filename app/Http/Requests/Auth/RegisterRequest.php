<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
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
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
      'role' => ['required', 'in:requisitioner,sps,vp_acad,auditor,accounting'],
      'department' => ['required', 'string', 'max:255'],
      'school_year' => ['nullable', 'string', 'max:50', 'regex:/^(\d{4}-\d{4}|\d+(st|nd|rd|th)\s+Year)$/i'],
      'student_number' => ['nullable', 'string', 'max:50', 'unique:users,student_number'],
    ];
  }

  /**
   * Get custom validation messages.
   *
   * @return array<string, string>
   */
  public function messages(): array
  {
    return [
      'name.required' => 'Please enter your full name.',
      'email.required' => 'Please enter your email address.',
      'email.email' => 'Please enter a valid email address.',
      'email.unique' => 'This email address is already registered.',
      'password.required' => 'Please enter a password.',
      'password.confirmed' => 'The password confirmation does not match.',
      'role.required' => 'Please select a role.',
      'role.in' => 'Please select a valid role.',
      'department.required' => 'Please enter your department.',
      'school_year.max' => 'School year must not exceed 50 characters.',
      'school_year.regex' => 'School year must be in format "2024-2025" or "1st Year", "2nd Year", etc.',
      'student_number.max' => 'Student number must not exceed 50 characters.',
      'student_number.unique' => 'This student number is already registered.',
    ];
  }
}
