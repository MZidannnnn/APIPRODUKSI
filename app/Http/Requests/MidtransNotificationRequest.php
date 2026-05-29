<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MidtransNotificationRequest extends FormRequest
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
            'order_id' => ['required', 'string'],
            'status_code' => ['required', 'string'],
            'gross_amount' => ['required', 'string'],
            'signature_key' => ['required', 'string'],
            'transaction_status' => ['required', 'string'],
            'payment_type' => ['nullable', 'string'],
            'transaction_id' => ['nullable', 'string'],
        ];
    }

    protected function passedValidation(): void
    {
        $expected = hash(
            'sha512',
            $this->input('order_id')
            . $this->input('status_code')
            . $this->input('gross_amount')
            . config('midtrans.server_key')
        );

        if (!hash_equals($expected, $this->input('signature_key'))) {
            throw new HttpResponseException(
                response()->json(['message' => 'Invalid signature'], 401)
            );
        }
    }
}
