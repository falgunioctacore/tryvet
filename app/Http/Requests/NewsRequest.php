<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewsRequest extends FormRequestHandle
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
            'location'=>'required|string',
            'date'=>'nullable',
            'time'=>'nullable',
            'news'=>'required|string',
            'description'=>'required|string',
            'links'=>'nullable|string',
            'user_id'=>'nullable|string',
            
        ];
    }

    // protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
    //     $errors=$validator->errors();
    //     throw new ValidationException($validator, response()->json([
    //         'status'=>0,
    //         'message'=>'validation is failed',
    //         'errors'=>$validator->errors()
    //     ],422),);
    // }
}
