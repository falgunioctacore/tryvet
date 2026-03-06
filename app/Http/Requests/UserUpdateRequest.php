<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequestHandle
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
            'name'=>'sometimes|string',
            'email'=>'sometimes|email|unique:users,email,'.$this->id,
            'password'=>'sometimes|string',
            'mobile_no'=>'sometimes|string',
            'buisness'=>'sometimes|string',
            'state'=>'sometimes|string',
            'city'=>'sometimes|string',
            'occupation'=>'sometimes|string',
            'device_id'=>'sometimes|string',
            'location'=>'sometimes|string',
            'father_name'=>'sometimes|string',
            'occupation_flag' => 'sometimes|integer|in:0,1',
            'district'=>'nullable|string',
            'taluka'=>'nullable|string',


        ];
    }
}
