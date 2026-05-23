<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusPesananRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $pesanan = $this->route('pesanan');

        return $pesanan
            && $this->user()
            && $this->user()->can('updateStatus', $pesanan);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_status_pesanan' => ['required', 'integer', 'exists:status_pesanan,id_status_pesanan'],
        ];
    }
}
