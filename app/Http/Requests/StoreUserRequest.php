<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|regex:/^[a-zA-Z ]+$/',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'nickname' => 'string|min:3|unique:users|regex:/^[a-zA-Z ]+$/',
            'reason_blocked' => 'nullable|string|max:255',
            'reason_reactivated' => 'nullable|string|max:255',
        ];
    }
}
