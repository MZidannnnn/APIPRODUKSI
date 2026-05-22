<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ChatAttachmentRule implements ValidationRule
{
    private const MIME_BY_EXT = [
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'gif' => ['image/gif'],
        'webp' => ['image/webp'],
        'pdf' => ['application/pdf'],
        'zip' => ['application/zip', 'application/x-zip-compressed'],
        'rar' => ['application/x-rar-compressed', 'application/vnd.rar'],
        'psd' => ['image/vnd.adobe.photoshop'],
        'ai' => ['application/vnd.adobe.illustrator', 'application/postscript'],
        'eps' => ['application/postscript'],
    ];

    private const IMAGE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    private const DESIGN_MIMES = [
        'application/pdf',
        'application/zip',
        'application/x-rar-compressed',
        'application/vnd.rar',
        'image/vnd.adobe.photoshop',
        'application/postscript',
        'application/vnd.adobe.illustrator',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            return;
        }

        $mime = $value->getMimeType() ?? '';
        $ext = strtolower($value->getClientOriginalExtension());
        $size = $value->getSize() ?? 0;

        $allowedMimes = self::MIME_BY_EXT[$ext] ?? null;
        if (! $allowedMimes) {
            $fail('Ekstensi file tidak didukung.');
            return;
        }

        if (! in_array($mime, $allowedMimes, true)) {
            $fail('Tipe MIME file tidak valid.');
            return;
        }

        $isImage = in_array($mime, self::IMAGE_MIMES, true);
        $isDesign = in_array($mime, self::DESIGN_MIMES, true);

        if (! $isImage && ! $isDesign) {
            $fail('Format file tidak didukung.');
            return;
        }

        if ($isImage && $size > 10 * 1024 * 1024) {
            $fail('Ukuran gambar maksimal 10 MB.');
            return;
        }

        if ($isDesign && $size > 50 * 1024 * 1024) {
            $fail('Untuk ukuran file di atas 50 MB, harap lampirkan tautan Google Drive, Dropbox, atau WeTransfer.');
        }
    }
}