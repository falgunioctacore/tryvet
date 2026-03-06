<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class NewsUpdateRequest extends FormRequestHandle
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
            'id'=>'required|numeric',
            'location'=>'sometimes|string',
            'date'=>'sometimes|date',
            'time'=>'sometimes',
            'user_id'=>'nullable|string',
            // 'datetime'=>'sometimes|datetime',
            'user_id'=>'nullable|string',
            'news'=>'sometimes|string',
            'description'=>'sometimes|string',
            'links'=>'sometimes|string'
        ];
    }
  
}
