<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePriceRequest extends FormRequest
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
            'main_group_id'=>'required|exists:main_groups,id',
            // 'state_id'=>'sometimes|exists:states,id',
            'title'=>'required',
            'title_id'=>'required|exists:groups,id',
            'category_id'=>'sometimes|exists:categories,id',
            'heading'=>'required',
            'date'=>'sometimes|date',
            'time'=>['sometimes', 'regex:/^(0[1-9]|1[0-2]):([0-5][0-9]) (AM|PM)$/'],
            'group'=>'sometimes|array',
            'group.*.district_id'=>'exists:districts,id',
            'group.*.price'=>'sometimes',
        ];
    }
}
