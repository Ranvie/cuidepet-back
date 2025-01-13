<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest{
    public function rules()
    {
        return [
            'username'             => 'required|string',
            'email'                => 'required|string|email|unique:tb_user,email',
            'password'             => 'required|string|min:6|confirmed:passwordConfirmation',
            'passwordConfirmation' => 'required|string'
        ];
    }
}
