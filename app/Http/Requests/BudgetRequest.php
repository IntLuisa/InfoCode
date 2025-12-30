<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BudgetRequest extends FormRequest
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
            'client_id' => 'required|exists:clients,id',
            'note' => 'nullable|string',
            'items' => 'required|array',
            'items.*.item_id' => 'required|integer',
            'items.*.item_name' => 'required|string',
            'items.*.item_type' => 'required|string',
            'items.*.item_code' => 'required|string',
            'items.*.amount' => 'required|numeric',
            'items.*.add_delivery' => 'nullable|boolean',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'observations' => 'nullable|string',
            'guarantees' => 'required|string',
            'days_valid_proposal' => 'nullable|integer|min:0',
            'percentage_payment' => 'nullable|numeric|min:0|max:100',
            'free_delivery' => 'nullable|boolean',
            'show_delivery' => 'nullable|boolean',
            'veicule_type' => 'nullable|string',
            'mileage' => 'nullable',
            'discount' => 'nullable|numeric',
            'status' => 'string|in:aberto,fechado,pending,canceled',
        ];
    }
}
