<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionRequest extends FormRequest
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
            // 'package_type'=>'required',
            'package_id'=>'required|exists:package_types,id',
             'main_group_id'=>'sometimes|exists:main_groups,id',
            'start_date'=>'nullable|date',
            'flag'=>'required|numeric|min:0|max:1',

        ];
    }
}
