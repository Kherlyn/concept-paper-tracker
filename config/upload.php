<?php

return [

  /*
    |--------------------------------------------------------------------------
    | File Upload Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for file uploads in the application,
    | including file size limits, allowed MIME types, and storage settings.
    |
    */

  /*
    |--------------------------------------------------------------------------
    | Maximum File Size
    |--------------------------------------------------------------------------
    |
    | The maximum file size allowed for uploads in bytes.
    | Default: 10MB (10 * 1024 * 1024 bytes)
    |
    */

  'max_file_size' => env('UPLOAD_MAX_FILE_SIZE', 10485760), // 10MB in bytes

  /*
    |--------------------------------------------------------------------------
    | Allowed MIME Types
    |--------------------------------------------------------------------------
    |
    | The MIME types that are allowed for concept paper attachments.
    | Currently restricted to PDF files only.
    |
    */

  'allowed_mime_types' => [
    'application/pdf',
  ],

  /*
    |--------------------------------------------------------------------------
    | Allowed File Extensions
    |--------------------------------------------------------------------------
    |
    | The file extensions that are allowed for concept paper attachments.
    |
    */

  'allowed_extensions' => [
    'pdf',
  ],

  /*
    |--------------------------------------------------------------------------
    | Storage Disk
    |--------------------------------------------------------------------------
    |
    | The storage disk to use for concept paper attachments.
    | This should reference a disk defined in config/filesystems.php
    |
    */

  'storage_disk' => env('UPLOAD_STORAGE_DISK', 'concept_papers'),

];
