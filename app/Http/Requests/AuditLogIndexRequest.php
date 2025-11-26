<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuditLogIndexRequest extends FormRequest
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
            'user_id'        => ['nullable', 'integer'],
            'action'         => ['nullable', 'string', 'max:255'],
            'auditable_type' => ['nullable', 'string', 'max:255'],
            'auditable_id'   => ['nullable', 'integer'],
            'date_from'      => ['nullable', 'date'],
            'date_to'        => ['nullable', 'date', 'after_or_equal:date_from'],
            'limit'          => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function filters(): array
    {
        return $this->only([
            'user_id',
            'action',
            'auditable_type',
            'auditable_id',
            'date_from',
            'date_to',
        ]);
    }
}
