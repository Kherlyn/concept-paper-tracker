<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnStageRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    $stage = $this->route('workflowStage');

    return $this->user()->canApproveStage($stage) &&
      in_array($stage->status, ['pending', 'in_progress']);
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'remarks' => ['required', 'string', 'max:1000'],
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
      'remarks.required' => 'Remarks are required when returning a stage to the previous step.',
      'remarks.string' => 'The remarks must be a valid text.',
      'remarks.max' => 'The remarks may not be greater than 1000 characters.',
    ];
  }
}
