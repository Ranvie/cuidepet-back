<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class RecoveryRequest extends FormRequest{
    public function rules()
    {
        return [
            'email' => 'required|string|email'
        ];
    }
}
