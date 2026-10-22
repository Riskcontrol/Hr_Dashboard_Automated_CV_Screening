<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Exception;

class FileUploadService
{
    private const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/msword'
    ];

    private const ALLOWED_EXTENSIONS = ['pdf', 'docx', 'doc'];
    private const MAX_FILE_SIZE = 10485760; // 10MB

    public function uploadCV(UploadedFile $file, string $applicantName): array
    {
        $this->validateFile($file);

        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = $this->generateUniqueFilename($applicantName, $extension);
        
        // Store file
        $path = $file->storeAs('cvs', $filename, 'local');

        return [
            'original_name' => $file->getClientOriginalName(),
            'stored_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType()
        ];
    }

    private function validateFile(UploadedFile $file): void
    {
        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new Exception('File size exceeds the maximum limit of 10MB');
        }

        // Check MIME type
        if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
            throw new Exception('Invalid file type. Only PDF, DOC, and DOCX files are allowed');
        }

        // Check extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            throw new Exception('Invalid file extension. Only PDF, DOC, and DOCX files are allowed');
        }

        // Check if file is corrupted
        if (!$file->isValid()) {
            throw new Exception('The uploaded file is corrupted or invalid');
        }
    }

    private function generateUniqueFilename(string $applicantName, string $extension): string
    {
        // Sanitize applicant name
        $sanitizedName = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $applicantName);
        $sanitizedName = substr($sanitizedName, 0, 50); // Limit length
        
        // Add timestamp and random string for uniqueness
        $timestamp = now()->format('Y_m_d_H_i_s');
        $random = substr(md5(uniqid()), 0, 8);
        
        return "{$sanitizedName}_{$timestamp}_{$random}.{$extension}";
    }

    public function deleteFile(string $path): bool
    {
        try {
            return Storage::disk('local')->delete($path);
        } catch (Exception $e) {
            return false;
        }
    }
}