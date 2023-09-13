<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the rules that apply to the request.
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules()
    {
        return [
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'products.required' => 'Products are required',
            'products.array' => 'Products must be an array',
            'products.*.product_id.required' => 'Product ID is required',
            'products.*.product_id.exists' => 'Product ID does not exist',
            'products.*.quantity.required' => 'Quantity is required',
            'products.*.quantity.integer' => 'Quantity must be an integer',
            'products.*.quantity.min' => 'Quantity must be at least 1',
        ];
    }
}
