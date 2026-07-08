<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d H:i:s'],
            'description' => ['nullable', 'string'],
            'entries' => ['required', 'array', 'min:2'],
            'entries.*.account_id' => ['required', 'numeric', Rule::exists('accounts', 'account_id')],
            'entries.*.amount' => ['required', 'numeric', 'gt:0'],
            'entries.*.type' => ['required', 'in:debit,credit'],
        ];
    }
}
