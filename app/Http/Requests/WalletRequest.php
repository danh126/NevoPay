<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletRequest extends FormRequest
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
        if ($this->isMethod('post')) {
            return [
                'user_id'   => ['required', 'exists:users,id'],
                'currency'  => ['required', 'string', 'max:5'],
                'is_active' => ['boolean'],
                'balance'   => ['numeric', 'min:0'],
            ];
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'currency'  => ['sometimes', 'string', 'max:5'],
                'is_active' => ['sometimes', 'boolean'],
            ];
        }

        if ($this->routeIs('wallets.update-active')) {
            return [
                'is_active' => ['required', 'boolean'],
            ];
        }

        return [];
    }
}
