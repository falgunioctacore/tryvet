<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class IdPackageTypeRequest extends FormRequest
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
            'id'=>'nullable|numeric',
            'main_group_id'=>'nullable|exists:main_groups,id'
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        $errors=$validator->errors();
        throw new ValidationException($validator, response()->json([
            'status'=>0,
            'message'=>'validation is failed',
            'errors'=>$validator->errors()
        ],422));
    }
}
