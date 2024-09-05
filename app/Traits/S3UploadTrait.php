<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait S3UploadTrait
{
    /**
     * Upload a file to the specified S3 bucket folder and delete the old file if provided.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder
     * @param string|null $oldFilePath
     * @return string|false
     */
    public function uploadToS3($file, $folder, $oldFilePath = null)
    {
        if (!$file->isValid()) {
            return false;  // Handle the error as needed
        }

        // Delete the old file if it exists
        if ($oldFilePath) {
            Storage::disk('s3')->delete($oldFilePath);
        }

        // Generate a unique file name
        $fileName = uniqid() . '_' . trim($file->getClientOriginalName());
        
        // Store the file on S3
        $path = Storage::disk('s3')->putFile($folder, $file, $fileName);
   
        return $path;
    }

    /**
     * Delete a file from S3.
     *
     * @param string $filePath
     * @return bool
     */
    public function deleteFromS3($filePath)
    {
        return Storage::disk('s3')->delete($filePath);
    }
}