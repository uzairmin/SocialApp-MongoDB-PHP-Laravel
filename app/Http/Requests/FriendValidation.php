<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FriendValidation extends FormRequest
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
            'email1'=>'required|string|email',
            'email2'=>'required|string|email'
        ];
    }
}
