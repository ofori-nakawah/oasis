<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $minWithdrawal = config('wallet.minimum_withdrawal', 10);
        
        return [
            'amount' => [
                'required',
                'numeric',
                "min:{$minWithdrawal}",
                function ($attribute, $value, $fail) {
                    $user = auth()->user();
                    $currentBalance = (float) $user->available_balance;
                    if ($value > $currentBalance) {
                        $fail('Insufficient balance. Your current balance is GHS ' . number_format($currentBalance, 2));
                    }
                },
            ],
            'account_number' => 'required|string',
            'bank_code' => 'required|string',
            'account_name' => 'required|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        $minWithdrawal = config('wallet.minimum_withdrawal', 10);
        
        return [
            'amount.required' => 'Please enter an amount to withdraw',
            'amount.numeric' => 'Amount must be a valid number',
            'amount.min' => "Minimum withdrawal amount is {$minWithdrawal} GHS",
            'account_number.required' => 'Account number is required',
            'bank_code.required' => 'Bank code is required',
            'account_name.required' => 'Account name is required',
            'account_name.max' => 'Account name must not exceed 255 characters',
        ];
    }
}
