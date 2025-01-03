<?php

namespace App\Http\Requests;
use App\Rules\PhoneRule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest {
    /**
     * Define se o usuário tem permissão na requisição
     * @return boolean
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Regras de validação para os dados da requisição, a partir do método usado.
     * @return String[]
     */
    public function rules()
    {
        $rules = [];

        switch ($this->method()) {
            case 'POST':
                $rules = $this->postRules();
                break;
            case 'PUT':
                $rules = $this->putRules();
                break;
        }

        return $rules;
    }

    private function postRules(){
        return [
            'username' => 'required|string',
            'email' => 'required|string|email|unique:tb_user,email',
            'password' => 'required|string|min:6|confirmed:passwordConfirmation',
            'passwordConfirmation' => 'required|string',
            'secondaryEmail' => 'string|nullable|email|unique:tb_user,secondary_email',
            'imageProfile' => 'string|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'mainPhone' => ['string','nullable', new PhoneRule()],
            'secondaryPhone' => ['string','nullable', new PhoneRule()]
        ];
    }

    private function putRules(){
        return [
            'username' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed:passwordConfirmation',
            'passwordConfirmation' => 'nullable|string',
            'secondaryEmail' => 'string|nullable|email|unique:tb_user,secondary_email',
            'imageProfile' => 'string|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'mainPhone' => ['string','nullable', new PhoneRule()],
            'secondaryPhone' => ['string','nullable', new PhoneRule()]
        ];
    }
}
