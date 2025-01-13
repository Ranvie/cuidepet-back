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
            'formId'                  => 'nullable|exists:tb_form,id',
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
            'animal.breedId'          => 'required|exists:tb_breed,id',
            'animal.specieId'         => 'required|exists:tb_specie,id',
        ];
    }

    private function putRules(){
        return [
            'type'                       => 'nullable|in:lost,donation',
            'description'                => 'nullable|string|max:1000',
            'mainImage'                  => 'nullable|string',
            'address'                    => ['nullable', 'string', 'max:255', new AddressRule()],
            'contactPhone'               => ['nullable', 'string', new PhoneRule()],
            'contactEmail'               => 'nullable|string|email|max:255',
            'lastSeenLatitude'           => 'nullable|numeric',
            'lastSeenLongitude'          => 'nullable|numeric',
            'announcementMedia'          => 'nullable|array',
            'announcementMedia.*.id'     => 'required|int|exists:tb_announcement_media,id',
            'announcementMedia.*.url'    => 'required|string',
            'announcementMedia.*.action' => 'required|in:UPD,ADD,DEL',
            'formId'                     => 'nullable|exists:tb_form,id',
            'animal'                     => 'nullable|array',
            'animal.name'                => 'nullable|string|max:255',
            'animal.gender'              => 'nullable|in:male,female',
            'animal.color'               => 'nullable|string|max:50',
            'animal.size'                => 'nullable|in:small,medium,large',
            'animal.age'                 => 'nullable|in:puppy,adult,senior',
            'animal.disability'          => 'nullable|boolean',
            'animal.vaccinated'          => 'nullable|boolean',
            'animal.dewormed'            => 'nullable|boolean',
            'animal.castrated'           => 'nullable|boolean',
            'animal.imageProfile'        => 'nullable|string',
            'animal.lastSeenDate'        => 'nullable|date',
            'animal.breedId'             => 'nullable|exists:tb_breed,id',
            'animal.specieId'            => 'nullable|exists:tb_specie,id',
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
