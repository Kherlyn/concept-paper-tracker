<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteStageRequest extends FormRequest
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
      'remarks' => ['nullable', 'string', 'max:1000'],
      'signature' => ['required', 'string'], // Base64 encoded signature image
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
      'remarks.string' => 'The remarks must be a valid text.',
      'remarks.max' => 'The remarks may not be greater than 1000 characters.',
      'signature.required' => 'A digital signature is required to approve this stage.',
      'signature.string' => 'The signature must be a valid format.',
    ];
  }
}
