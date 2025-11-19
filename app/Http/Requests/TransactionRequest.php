<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
        $rules = [
            'wallet_number' => 'required|string|exists:wallets,wallet_number',
            'amount'        => 'required|numeric|min:0.01',
        ];

        if ($this->isMethod('post') && $this->routeIs('transactions.transfer')) {
            $rules['to_wallet_number'] = 'required|string|exists:wallets,wallet_number|different:wallet_number';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'wallet_number.required' => 'Wallet number is required.',
            'wallet_number.exists'   => 'Wallet not found.',
            'amount.required'        => 'Amount is required.',
            'amount.numeric'         => 'Amount must be numeric.',
            'amount.min'             => 'Amount must be greater than 0.',
            'to_wallet_number.required' => 'Receiver wallet is required.',
            'to_wallet_number.different' => 'Cannot transfer to the same wallet.',
        ];
    }
}
