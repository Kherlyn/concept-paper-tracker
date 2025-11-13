<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadAttachmentRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true; // Authorization is handled by policies
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    $maxSize = config('upload.max_file_size');
    $maxSizeKB = round($maxSize / 1024);
    $allowedExtensions = implode(',', config('upload.allowed_extensions'));
    $allowedMimeTypes = implode(',', config('upload.allowed_mime_types'));

    return [
      'file' => [
        'required',
        'file',
        "max:{$maxSizeKB}",
        "mimes:{$allowedExtensions}",
        "mimetypes:{$allowedMimeTypes}",
      ],
    ];
  }

  /**
   * Get custom messages for validator errors.
   *
   * @return array<string, string>
   */
  public function messages(): array
  {
    $maxSizeMB = round(config('upload.max_file_size') / 1024 / 1024, 2);

    return [
      'file.required' => 'Please select a file to upload.',
      'file.file' => 'The uploaded file is invalid.',
      'file.max' => "The file size must not exceed {$maxSizeMB}MB.",
      'file.mimes' => 'Only PDF files are allowed.',
      'file.mimetypes' => 'Only PDF files are allowed.',
    ];
  }
}
