<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStateDPMultiplesRequest extends FormRequestHandle
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
            'entry_date'=>'nullable|date',
            'entry_time'=>['nullable', 'regex:/^(0[1-9]|1[0-2]):([0-5][0-9]) (AM|PM)$/'],
            'districts'=>'required|array',
            'districts.*.district_id'=>'exists:districts,id',
            'districts.*.price'=>'required',
            
        ];
    }
}
