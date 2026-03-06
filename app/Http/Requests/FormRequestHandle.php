<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class FormRequestHandle extends FormRequest
{
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        $errors=$validator->errors();
        throw new ValidationException($validator, response()->json([
            'status'=>0,
            'message'=>'validation is failed',
            'errors'=>$validator->errors()
        ],422));
    }
}
