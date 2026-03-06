<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStateDistrictPricingRequest extends FormRequest
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
            'id'=>'required',
            'state_id'=>'sometimes|exists:states,id',
            'district_id'=>'sometimes|exists:districts,id',
            'price'=>'sometimes',
            'entry_date'=>'sometimes|date',
            'entry_time'=>'sometimes'
        ];
    }
}
