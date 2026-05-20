<?php

namespace App\Http\Requests;

use App\Rules\ChatAttachmentRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class StoreChatMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $lampiran = $this->file('lampiran');
        if ($lampiran instanceof UploadedFile) {
            $this->files->set('lampiran', [$lampiran]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'isi_pesan' => ['nullable', 'string', 'max:2000', 'required_without:lampiran'],
            'lampiran' => ['nullable', 'array', 'max:5', 'required_without:isi_pesan'],
            'lampiran.*' => [
                'file',
                'mimes:jpg,jpeg,png,gif,webp,pdf,zip,rar,psd,ai,eps',
                new ChatAttachmentRule(),
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $files = $this->file('lampiran', []);
            if (! is_array($files) || count($files) === 0) {
                return;
            }

            $totalBytes = 0;
            foreach ($files as $file) {
                $totalBytes += $file->getSize() ?: 0;
            }

            if ($totalBytes > 100 * 1024 * 1024) {
                $validator->errors()->add(
                    'lampiran',
                    'Total ukuran lampiran maksimal 100 MB per pengiriman.'
                );
            }

            $projectExt = ['zip', 'rar', 'psd', 'ai', 'eps'];
            $hasProject = false;
            foreach ($files as $file) {
                $ext = strtolower($file->getClientOriginalExtension());
                if (in_array($ext, $projectExt, true)) {
                    $hasProject = true;
                    break;
                }
            }

            if ($hasProject && count($files) > 1) {
                $validator->errors()->add(
                    'lampiran',
                    'File proyek (ZIP/RAR/PSD/AI/EPS) hanya boleh 1 per pengiriman.'
                );
            }
        });
    }

    public function attributes(): array
    {
        return [
            'isi_pesan' => 'pesan',
            'lampiran' => 'lampiran',
        ];
    }
}
