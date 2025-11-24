<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConceptPaperRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return $this->user()->hasRole('requisitioner');
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'department' => ['required', 'string', 'max:255'],
      'title' => ['required', 'string', 'max:1000'],
      'nature_of_request' => ['required', 'in:regular,urgent,emergency'],
      'students_involved' => ['required', 'boolean'],
      'deadline_option' => ['required', 'string', 'in:1_week,2_weeks,1_month,2_months,3_months'],
      'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'], // 10MB max
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
      'department.required' => 'The department field is required.',
      'department.max' => 'The department may not be greater than 255 characters.',
      'title.required' => 'The concept paper title is required.',
      'title.max' => 'The title may not be greater than 1000 characters.',
      'nature_of_request.required' => 'Please select the nature of request.',
      'nature_of_request.in' => 'The nature of request must be regular, urgent, or emergency.',
      'students_involved.required' => 'Please indicate whether students are involved.',
      'students_involved.boolean' => 'The students involved field must be yes or no.',
      'deadline_option.required' => 'Please select a deadline option.',
      'deadline_option.in' => 'The deadline option must be one of the predefined options.',
      'attachment.file' => 'The attachment must be a valid file.',
      'attachment.mimes' => 'The attachment must be a PDF or Word document (.pdf, .doc, .docx).',
      'attachment.max' => 'The attachment may not be greater than 10MB.',
    ];
  }

  /**
   * Get custom attributes for validator errors.
   *
   * @return array<string, string>
   */
  public function attributes(): array
  {
    return [
      'nature_of_request' => 'nature of request',
    ];
  }
}
