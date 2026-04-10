<?php

namespace App\Http\Requests;

use App\Rules\PhoneRule;
use App\Rules\ZipcodeRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Classe de requisição para validação de dados relacionados a anúncios.
 * Define as regras de validação para criação e edição de anúncios, incluindo informações do animal, endereço e contato.
 */
class AnnouncementRequest extends FormRequest {
  
  /**
   * Define se o usuário tem permissão na requisição
   * @return bool
   */
  public function authorize() :bool {
    return true;
  }

  /**
   * Regras de validação para os dados da requisição, a partir do método usado.
   * @return array
   */
  public function rules() :array {
    $rules = [];

    $rules = match ($this->method()) {
      'POST'  => $this->postRules(),
      'PUT'   => $this->putRules(),
      default => [],
    };

    return $rules;
  }

  /** 
   * Regras de validação para o método POST
   * @return array
   */
  private function postRules() :array {
    return [
      'type'                     => 'required|in:lost,donation',
      'description'              => 'nullable|string|max:1000',
      'mainImage'                => 'nullable|string',
      'address'                  => 'required|array',
      'address.zipCode'          => ['required', 'string', new ZipcodeRule()],
      'address.street'           => 'nullable|string|max:255',
      'address.neighborhood'     => 'nullable|string|max:255',
      'address.number'           => 'required|string|max:10',
      'address.complement'       => 'nullable|string|max:255',
      'contactPhone'             => ['nullable', 'string', new PhoneRule()],
      'contactEmail'             => 'nullable|string|email|max:255',
      'announcementMedia'        => 'nullable|array',
      'announcementMedia.*.file' => 'required|mimes:jpg,jpeg,png,webp|dimensions:min_width=300,max_width=1000,min_height=300,max_height=1000|max:5120',
      'formId'                   => 'nullable|exists:tb_form,id',
      'animal'                   => 'required|array',
      'animal.name'              => 'required|string|max:255',
      'animal.gender'            => 'required|in:male,female',
      'animal.color'             => 'required|string|max:50',
      'animal.size'              => 'required|in:small,medium,large',
      'animal.age'               => 'required|in:puppy,adult,senior',
      'animal.disability'        => 'nullable|boolean',
      'animal.vaccinated'        => 'nullable|boolean',
      'animal.dewormed'          => 'nullable|boolean',
      'animal.castrated'         => 'nullable|boolean',
      'animal.imageProfile'      => 'required|string',
      'animal.lastSeenDate'      => 'missing_if:type,donation|date',
      'animal.breedId'           => 'required|exists:tb_breed,id',
      'animal.specieId'          => 'required|exists:tb_specie,id',
    ];
  }

  /** 
   * Regras de validação para o método PUT
   * @return array
   */
  private function putRules() :array {
    return [
      'type'                       => 'nullable|in:lost,donation',
      'description'                => 'nullable|string|max:1000',
      'mainImage'                  => 'nullable|string',
      'address'                    => 'nullable|array',
      'address.zipCode'            => ['nullable', 'string', new ZipcodeRule()],
      'address.street'             => 'nullable|string|max:255',
      'address.neighborhood'       => 'nullable|string|max:255',
      'address.number'             => 'nullable|string|max:10',
      'address.complement'         => 'nullable|string|max:255',
      'contactPhone'               => ['nullable', 'string', new PhoneRule()],
      'contactEmail'               => 'nullable|string|email|max:255',
      'announcementMedia'          => 'nullable|array',
      'announcementMedia.*.id'     => 'required_unless:announcementMedia.*.action,ADD|int',
      'announcementMedia.*.file'   => 'required|mimes:jpg,jpeg,png,webp|dimensions:min_width=300,max_width=2000,min_height=300,max_height=2000|max:5120',
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
      'animal.lastSeenDate'        => 'missing_if:type,donation|date',
      'animal.breedId'             => 'nullable|exists:tb_breed,id',
      'animal.specieId'            => 'nullable|exists:tb_specie,id',
    ];
  }

  /** Mensagens de erro personalizadas para as regras de validação
   * @return array
   */
  public function messages() :array {
    return [
      'type'                     => 'Os tipos permitidos são lost ou donation',
      'status'                   => 'Os status permitidos para o status são open e closed',
      'animal.gender'            => 'Os tipos permitidos são male e female',
      'animal.size'              => 'Os tamanhos permitidos são small, medium ou large',
      'animal.age'               => 'As descrições de idade permitidas são puppy, adult e senior',
      'announcementMedia.*.file' => 'Os arquivos devem ser do tipo jpg, jpeg, png ou webp, com dimensões entre 300x300 e 1000x1000 pixels, e tamanho máximo de 5MB.',
    ];
  }
}
