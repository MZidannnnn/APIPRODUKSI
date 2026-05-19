<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ChatAttachmentRule implements ValidationRule
{

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
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
        if (! $value instanceof UploadedFile) {
            return;
        }

        $mime = $value->getMimeType() ?? '';
        $size = $value->getSize() ?? 0;

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
