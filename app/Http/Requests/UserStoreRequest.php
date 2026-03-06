<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequestHandle
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
            'name'=>'required|string',
            'email'=>'required|email|unique:users',
            'password'=>'required',
            'mobile_no'=>'required|min:10',
            'buisness'=>'nullable|string',
            'state'=>'nullable|string',
            'city'=>'nullable|string',
            'occupation'=>'nullable|string',
            'device_id'=>'nullable|string',
            'location'=>'nullable|string',
            'father_name'=>'nullable|string',
            'occupation_flag' => 'nullable|integer|in:0,1',
            'district'=>'nullable|string',
            'taluka'=>'nullable|string',
        ];
    }
}
