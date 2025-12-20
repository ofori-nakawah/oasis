<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InitializePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => ['required', 'numeric', 'min:1'],
            'channel' => ['required', 'string', 'in:card,bank,ussd,qr,mobile_money'],
            'email' => ['required', 'email'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'metadata' => ['sometimes', 'array'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 1.',
            'channel.required' => 'The channel field is required.',
            'channel.in' => 'The selected channel is invalid. Allowed values: card, bank, ussd, qr, mobile_money.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'currency.size' => 'The currency must be exactly 3 characters.',
            'metadata.array' => 'The metadata must be an array.',
        ];
    }
}
