<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function rules()
    {
        return [
            'password'             => 'required|string|min:6|confirmed:passwordConfirmation',
            'passwordConfirmation' => 'required|string'
        ];
    }
}
