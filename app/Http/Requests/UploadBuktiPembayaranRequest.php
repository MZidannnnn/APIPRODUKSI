<?php

namespace App\Http\Requests;

use App\Models\Pembayaran;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UploadBuktiPembayaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pembayaran = $this->route('pembayaran');

        if (!$pembayaran instanceof Pembayaran) {
            return false;
        }

        $pembayaran->loadMissing('pesanan');
        return (int) $pembayaran->pesanan?->id_pengguna === (int) $this->user()?->id_pengguna;
    }

    public function rules(): array
    {
        return [
            'bukti_bayar' => [
                'required',
                'file',
                'max:5120',
                'mimes:jpg,jpeg,png,pdf',
                'mimetypes:image/jpeg,image/png,application/pdf',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $file = $this->file('bukti_bayar');
                if (!$file) {
                    return;
                }

                $realMime = $file->getMimeType();
                $allowed = ['image/jpeg', 'image/png', 'application/pdf'];

                if (!in_array($realMime, $allowed, true)) {
                    $validator->errors()->add(
                        'bukti_bayar',
                        'Tipe file tidak valid. Gunakan JPG, PNG, atau PDF.'
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'bukti_bayar.required' => 'File bukti bayar wajib diunggah.',
            'bukti_bayar.file' => 'Bukti bayar harus berupa file.',
            'bukti_bayar.max' => 'Ukuran file maksimal 5 MB.',
            'bukti_bayar.mimes' => 'Ekstensi file harus JPG, PNG, atau PDF.',
            'bukti_bayar.mimetypes' => 'MIME file tidak valid.',
        ];
    }
}