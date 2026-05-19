<?php

namespace App\Http\Requests;

use App\Rules\ChatAttachmentRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreChatMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'lampiran' => ['nullable', 'file', 'required_without:isi_pesan', new ChatAttachmentRule()],
        ];
    }

    public function attributes(): array
    {
        return [
            'isi_pesan' => 'pesan',
            'lampiran' => 'lampiran',
        ];
    }
}
