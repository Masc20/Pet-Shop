<?php
/**
 * Pawfect Pet Shop - File Upload Handler
 * Handles secure file uploads with validation
 */

class Upload {
    
    private $uploadDir;
    private $allowedTypes;
    private $maxFileSize;
    private $errors = [];
    
    public function __construct($uploadDir = null, $allowedTypes = null, $maxFileSize = 10485760) {
        // Set default upload directory
        if ($uploadDir === null) {
            $uploadDir = 'uploads/';
        }
        
        $this->uploadDir = rtrim($uploadDir, '/') . '/';
        
        // Set default allowed types
        $this->allowedTypes = $allowedTypes ?: [
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf', 'text/plain', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        $this->maxFileSize = $maxFileSize;
        
        // Create upload directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    public function uploadFile($file, $subfolder = '', $customName = null) {
        $this->errors = [];
        
        // Validate file
        if (!$this->validateFile($file)) {
            return false;
        }
        
        // Create subfolder if specified
        $targetDir = $this->uploadDir;
        if ($subfolder) {
            $targetDir .= trim($subfolder, '/') . '/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
        }
        
        // Generate filename
        $filename = $this->generateFilename($file, $customName);
        $targetPath = $targetDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Set proper permissions
            chmod($targetPath, 0644);
            
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $targetPath,
                'url' => $this->getFileUrl($subfolder . '/' . $filename),
                'size' => $file['size'],
                'type' => $file['type']
            ];
        } else {
            $this->errors[] = 'Failed to move uploaded file';
            return false;
        }
    }
    
    public function uploadMultiple($files, $subfolder = '', $customNames = []) {
        $results = [];
        
        // Handle multiple file upload format
        if (isset($files['name']) && is_array($files['name'])) {
            $fileCount = count($files['name']);
            
            for ($i = 0; $i < $fileCount; $i++) {
                $file = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];
                
                $customName = isset($customNames[$i]) ? $customNames[$i] : null;
                $result = $this->uploadFile($file, $subfolder, $customName);
                
                if ($result) {
                    $results[] = $result;
                }
            }
        }
        
        return $results;
    }
    
    public function uploadImage($file, $subfolder = 'images', $customName = null, $resize = null) {
        // Set allowed types to images only
        $originalTypes = $this->allowedTypes;
        $this->allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        
        $result = $this->uploadFile($file, $subfolder, $customName);
        
        // Restore original allowed types
        $this->allowedTypes = $originalTypes;
        
        if ($result && $resize) {
            $this->resizeImage($result['path'], $resize['width'], $resize['height'], $resize['quality'] ?? 85);
        }
        
        return $result;
    }
    
    public function deleteFile($filePath) {
        $fullPath = $this->uploadDir . ltrim($filePath, '/');
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }
    
    public function getFileUrl($filePath) {
        // Remove leading slash and upload directory from path if present
        $filePath = ltrim($filePath, '/');
        if (strpos($filePath, $this->uploadDir) === 0) {
            $filePath = substr($filePath, strlen($this->uploadDir));
        }
        
        $baseUrl = get_base_url();
        return $baseUrl . '/' . $this->uploadDir . $filePath;
    }
    
    public function getFileInfo($filePath) {
        $fullPath = $this->uploadDir . ltrim($filePath, '/');
        
        if (!file_exists($fullPath)) {
            return false;
        }
        
        return [
            'path' => $fullPath,
            'url' => $this->getFileUrl($filePath),
            'size' => filesize($fullPath),
            'type' => mime_content_type($fullPath),
            'modified' => filemtime($fullPath),
            'exists' => true
        ];
    }
    
    private function validateFile($file) {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadErrorMessage($file['error']);
            return false;
        }
        
        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            $this->errors[] = 'File size exceeds maximum allowed size of ' . $this->formatFileSize($this->maxFileSize);
            return false;
        }
        
        // Check file type
        $fileType = mime_content_type($file['tmp_name']);
        if (!in_array($fileType, $this->allowedTypes)) {
            $this->errors[] = 'File type not allowed. Allowed types: ' . implode(', ', $this->allowedTypes);
            return false;
        }
        
        // Additional security checks for images
        if (strpos($fileType, 'image/') === 0) {
            $imageInfo = getimagesize($file['tmp_name']);
            if (!$imageInfo) {
                $this->errors[] = 'Invalid image file';
                return false;
            }
        }
        
        return true;
    }
    
    private function generateFilename($file, $customName = null) {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        
        if ($customName) {
            $filename = $this->sanitizeFilename($customName);
        } else {
            $filename = $this->sanitizeFilename(pathinfo($file['name'], PATHINFO_FILENAME));
        }
        
        // Add timestamp to prevent conflicts
        $filename .= '_' . time();
        
        // Add random string for extra uniqueness
        $filename .= '_' . substr(md5(uniqid()), 0, 8);
        
        return $filename . '.' . strtolower($extension);
    }
    
    private function sanitizeFilename($filename) {
        // Remove special characters and spaces
        $filename = preg_replace('/[^a-zA-Z0-9\-_]/', '', $filename);
        
        // Limit length
        return substr($filename, 0, 50);
    }
    
    private function getUploadErrorMessage($errorCode) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        
        return $errors[$errorCode] ?? 'Unknown upload error';
    }
    
    private function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    private function resizeImage($imagePath, $newWidth, $newHeight, $quality = 85) {
        $imageInfo = getimagesize($imagePath);
        
        if (!$imageInfo) {
            return false;
        }
        
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $imageType = $imageInfo[2];
        
        // Calculate new dimensions maintaining aspect ratio
        $aspectRatio = $originalWidth / $originalHeight;
        
        if ($newWidth / $newHeight > $aspectRatio) {
            $newWidth = $newHeight * $aspectRatio;
        } else {
            $newHeight = $newWidth / $aspectRatio;
        }
        
        // Create image resource based on type
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($imagePath);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($imagePath);
                break;
            default:
                return false;
        }
        
        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Resize image
        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        
        // Save resized image
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($newImage, $imagePath, $quality);
                break;
            case IMAGETYPE_PNG:
                imagepng($newImage, $imagePath);
                break;
            case IMAGETYPE_GIF:
                imagegif($newImage, $imagePath);
                break;
        }
        
        // Clean up memory
        imagedestroy($sourceImage);
        imagedestroy($newImage);
        
        return true;
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    public function setAllowedTypes($types) {
        $this->allowedTypes = $types;
    }
    
    public function setMaxFileSize($size) {
        $this->maxFileSize = $size;
    }
    
    public function setUploadDir($dir) {
        $this->uploadDir = rtrim($dir, '/') . '/';
        
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
}
?>
