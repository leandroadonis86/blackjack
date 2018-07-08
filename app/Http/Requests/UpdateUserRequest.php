<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$this->user->id,
            'password' => 'required|min:6|confirmed',
            'nickname' => 'required|string|min:3',
            'admin' => 'integer|max:1',
            'blocked' => 'integer|max:1',
            'reason_blocked' => 'nullable|string|max:255',
            'reason_reactivated' => 'nullable|string|max:255',
            'total_points' => 'integer|max:11',
            'total_games_played' => 'integer|max:11',
        ];
    }
}
