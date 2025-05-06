<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class UserFormRequest extends FormRequest{
    public function rules()
    {
        return [
            'title'    => 'required|string|max:255',
            'payload'  => 'required|string|max:10000'
        ];
    }
}
