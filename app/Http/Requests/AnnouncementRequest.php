<?php

namespace App\Http\Requests;

use App\Rules\AddressRule;
use App\Rules\PhoneRule;
use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends FormRequest
{
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
            'type'              => 'in:lost,donation',
            'description'       => 'nullable|string|max:1000',
            'mainImage'         => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'address'           => ['string', 'max:255', new AddressRule()],
            'contactPhone'      => ['nullable', 'string', new PhoneRule()],
            'contactEmail'      => 'nullable|string|email|max:255',
            'lastSeenLatitude'  => 'numeric',
            'lastSeenLongitude' => 'numeric',
        ];
    }

    private function putRules(){
        return [
            'type'              => 'in:lost,donation',
            'description'       => 'nullable|string|max:1000',
            'mainImage'         => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'address'           => ['nullable', 'string', 'max:255', new AddressRule()],
            'contactPhone'      => ['nullable', 'string', new PhoneRule()],
            'contactEmail'      => 'nullable|string|email|max:255',
            'lastSeenLatitude'  => 'nullable|numeric',
            'lastSeenLongitude' => 'nullable|numeric',
        ];
    }

    public function messages(){
        return [
            'type' => "Os tipos permitidos são lost ou donation"
        ];
    }
}
