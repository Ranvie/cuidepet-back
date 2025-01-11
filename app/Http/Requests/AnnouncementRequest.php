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
            'type'                    => 'required|in:lost,donation',
            'description'             => 'nullable|string|max:1000',
            'mainImage'               => 'nullable|string',
            'address'                 => ['required', 'string', 'max:255', new AddressRule()],
            'contactPhone'            => ['nullable', 'string', new PhoneRule()],
            'contactEmail'            => 'nullable|string|email|max:255',
            'lastSeenLatitude'        => 'required|numeric',
            'lastSeenLongitude'       => 'required|numeric',
            'announcementMedia'       => 'nullable|array',
            'announcementMedia.*.url' => 'required|string',
            'animal'                  => 'required|array',
            'animal.name'             => 'required|string|max:255',
            'animal.gender'           => 'required|in:male,female',
            'animal.color'            => 'required|string|max:50',
            'animal.size'             => 'required|in:small,medium,large',
            'animal.age'              => 'required|in:puppy,adult,senior',
            'animal.disability'       => 'nullable|boolean',
            'animal.vaccinated'       => 'nullable|boolean',
            'animal.dewormed'         => 'nullable|boolean',
            'animal.castrated'        => 'nullable|boolean',
            'animal.imageProfile'     => 'required|string',
            'animal.lastSeenDate'     => 'required|date',
            'breed'                   => 'required|array',
            'breed.id'                => 'required|exists:tb_breed,id', //TODO: faz sentido ter o id? Ele apenas vai juntar ao que já existe no banco, talvez seja melhor pegar por nome
            'breed.name'              => 'nullable|string|max:255',
            'specie'                  => 'required|array',
            'specie.id'               => 'required|exists:tb_specie,id', //TODO: faz sentido ter o id?
            'specie.name'             => 'nullable|string|max:255',
        ];
    }

    private function putRules(){
        return [
            'type'              => 'nullable|in:lost,donation',
            'description'       => 'nullable|string|max:1000',
            'mainImage'         => 'nullable|url',
            'address'           => ['nullable', 'string', 'max:255', new AddressRule()],
            'contactPhone'      => ['nullable', 'string', new PhoneRule()],
            'contactEmail'      => 'nullable|string|email|max:255',
            'lastSeenLatitude'  => 'nullable|numeric',
            'lastSeenLongitude' => 'nullable|numeric',
            'status'            => 'nullable|in:open,closed',

            'announcementMedia'     => 'nullable|array',
            'announcementMedia.url' => 'nullable|url',

            'animal'              => 'nullable|array',
            'animal.name'         => 'nullable|string|max:255',
            'animal.gender'       => 'nullable|in:male,female',
            'animal.color'        => 'nullable|string|max:50',
            'animal.size'         => 'nullable|in:small,medium,large',
            'animal.age'          => 'nullable|in:puppy,adult,senior',
            'animal.disability'   => 'nullable|boolean',
            'animal.vaccinated'   => 'nullable|boolean',
            'animal.dewormed'     => 'nullable|boolean',
            'animal.castrated'    => 'nullable|boolean',
            'animal.imageProfile' => 'nullable|url',
            'animal.lastSeenDate' => 'nullable|date',

            'breed'      => 'nullable|array',
            'breed.id'   => 'nullable|exists:tb_breed,id',
            'breed.name' => 'nullable|string|max:255',

            'specie'      => 'nullable|array',
            'specie.id'   => 'nullable|exists:tb_species,id',
            'specie.name' => 'nullable|string|max:255',
        ];
    }

    public function messages(){
        return [
            'type'          => 'Os tipos permitidos são lost ou donation',
            'status'        => 'Os status permitidos para o status são open e closed',
            'animal.gender' => 'Os tipos permitidos são male e female',
            'animal.size'   => 'Os tamanhos permitidos são small, medium ou large',
            'animal.age'    => 'As descrições de idade permitidas são puppy, adult e senior'
        ];
    }
}
