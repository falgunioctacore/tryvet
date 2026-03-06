<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStateDistrictPricingRequest extends FormRequestHandle
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
            'state_id'=>'required|exists:states,id',
            'district_id'=>'required|exists:districts,id',
            'price'=>'required',
            'entry_date'=>'nullable|date',
            'entry_time'=>'nullable'
        ];
    }
}
