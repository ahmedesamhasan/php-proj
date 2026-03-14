<?php

declare(strict_types=1);

final class ImageService
{
    public function ensureDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    }

    public function storeUploadedImage(array $file, string $directory, ?string $oldImage = null): ?string
    {
        if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return $oldImage;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Image upload failed.');
        }

        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            throw new RuntimeException('Image size must be 2MB or less.');
        }

        $imageInfo = @getimagesize($file['tmp_name']);
        if (!$imageInfo || !isset($imageInfo['mime'])) {
            throw new RuntimeException('Uploaded file is not a valid image.');
        }

        $mimeType = (string) $imageInfo['mime'];
        $this->ensureDirectory($directory);

        $safeName = 'product_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.jpg';
        $targetPath = rtrim($directory, '/') . '/' . $safeName;

        if ($this->canResize($mimeType)) {
            $this->resizeAndSave($file['tmp_name'], $targetPath, $mimeType, 1200, 1200, 82);
        } else {
            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                throw new RuntimeException('Failed to save image.');
            }
        }

        if ($oldImage) {
            $oldPath = dirname($directory) . '/' . ltrim($oldImage, '/');
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        return 'images/' . $safeName;
    }

    private function canResize(string $mimeType): bool
    {
        return function_exists('imagecreatetruecolor')
            && in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true);
    }

    private function resizeAndSave(string $sourcePath, string $targetPath, string $mimeType, int $maxWidth, int $maxHeight, int $quality): void
    {
        [$width, $height] = getimagesize($sourcePath);

        $sourceImage = match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($sourcePath),
            'image/png' => imagecreatefrompng($sourcePath),
            'image/webp' => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($sourcePath) : false,
            'image/gif' => imagecreatefromgif($sourcePath),
            default => false,
        };

        if (!$sourceImage) {
            throw new RuntimeException('Could not process the uploaded image.');
        }

        $scale = min($maxWidth / $width, $maxHeight / $height, 1);
        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);
        imagecopyresampled($canvas, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        imagejpeg($canvas, $targetPath, $quality);

        imagedestroy($sourceImage);
        imagedestroy($canvas);
    }
}
