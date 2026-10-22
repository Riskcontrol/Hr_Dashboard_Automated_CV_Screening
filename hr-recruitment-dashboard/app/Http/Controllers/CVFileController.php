<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class CVFileController extends Controller
{
    public function serveFile($encodedPath)
    {
        try {
            // Decode the base64 encoded path
            $filePath = base64_decode($encodedPath);
            
            Log::info('CV file request', [
                'encoded_path' => $encodedPath,
                'decoded_path' => $filePath
            ]);
            
            // Validate the file path (security check)
            if (!$this->isValidCVPath($filePath)) {
                Log::warning('Invalid CV file path requested', ['path' => $filePath]);
                return response()->json(['error' => 'Invalid file path'], 403);
            }
            
            // Check if file exists in storage
            if (!Storage::exists($filePath)) {
                Log::warning('CV file not found', ['path' => $filePath]);
                return response()->json(['error' => 'File not found'], 404);
            }
            
            // Get file content and return as response
            $fileContent = Storage::get($filePath);
            $fileName = basename($filePath);
            
            // Determine content type
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $contentType = $this->getContentType($extension);
            
            Log::info('Serving CV file', [
                'path' => $filePath,
                'size' => strlen($fileContent),
                'content_type' => $contentType
            ]);
            
            return Response::make($fileContent, 200, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                'Content-Length' => strlen($fileContent),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error serving CV file', [
                'encoded_path' => $encodedPath,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
    
    private function isValidCVPath($filePath)
    {
        // Ensure the path starts with 'cvs/' directory
        if (!str_starts_with($filePath, 'cvs/')) {
            return false;
        }
        
        // Ensure no directory traversal attempts
        if (str_contains($filePath, '..') || str_contains($filePath, '//')) {
            return false;
        }
        
        // Check allowed file extensions
        $allowedExtensions = ['pdf', 'doc', 'docx'];
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        return in_array($extension, $allowedExtensions);
    }
    
    private function getContentType($extension)
    {
        $contentTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
        
        return $contentTypes[$extension] ?? 'application/octet-stream';
    }
}
