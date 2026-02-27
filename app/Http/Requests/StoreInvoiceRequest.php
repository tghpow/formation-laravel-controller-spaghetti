<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:120'],
            'client_name' => ['required', 'string', 'max:120'],
            'client_email' => ['nullable', 'email', 'max:255'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],

            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.label' => ['required', 'string', 'max:200'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:9999'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:1000000'],
        ];
    }
}
