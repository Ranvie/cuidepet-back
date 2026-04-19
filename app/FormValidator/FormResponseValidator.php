<?php

namespace App\FormValidator;

use App\Exceptions\BusinessException;
use App\Http\Enums\FormFieldsLength;
use App\Utils\Functions;

/**
 * Serviço de validação do formulário.
 */
class FormResponseValidator extends FormValidatorAbstract {

  /**
   * Método Construtor
   * @param array $requestPayload
   * @param array $originalPayload
   */
  public function __construct(
    private array $requestPayload,
    private array $originalPayload
  ) {}

  /**
   * Inicia processo de validação do formulário.
   * @return string
   * @throws BusinessException Se a estrutura do formulário for diferente do formulário original ou se algum campo obrigatório estiver ausente ou inválido.
   */
  public function resolve() :string {
    $this->validateFormStructure();

    foreach($this->requestPayload['pages'] ?? [] as $pageIndex => $page){  
      foreach($page['inputs'] ?? [] as $inputIndex => $input) {
        $field = "pages.{$pageIndex}.inputs.{$inputIndex}.value";

        if(!isset($input['value']))
          throw new BusinessException("O campo '{$field}' deve estar presente.", 400);

        if($input['required'] && empty($input['value']))
          throw new BusinessException("O campo '{$field}' é obrigatório e não pode estar vazio.", 400);

        match($input['type']){
          'checkbox', 'radio' => $this->validateCheckboxOrRadio($field, $input),
          'text', 'textarea'  => $this->validateText($field, $input),
          'number'            => $this->validateNumber($field, $input),
          'date'              => $this->validateDate($field, $input),
          'dropdown'          => $this->validateValueInOptions($field, $input),
          default             => throw new BusinessException("Tipo de campo '{$field}' não é suportado.", 400)
        };
      }
    }

    return "";
  }

  /**
   * Valida campos do tipo checkbox ou radio button.
   * @param  string $field Campo atual sendo validado, usado para mensagens de erro.
   * @param  array  $input Dados do campo a ser validado, incluindo tipo, valor, opções e se é obrigatório.
   * @return void
   * @throws BusinessException Se o valor não for um array, se o número de opções selecionadas for maior que o permitido ou se alguma opção selecionada for inválida.
   * Observação: Para campos do tipo 'checkbox', o número de opções selecionadas não pode ser maior que o número de opções disponíveis. Para campos do tipo 'radio', apenas uma opção pode ser selecionada.
   */
  private function validateCheckboxOrRadio(string $field, array $input) :void {
    $inputLength = $input['type'] === 'checkbox'
      ? \count($input['options'] ?? [])
      : FormFieldsLength::MAX_RADIO;
  
    $this->validateArray($field, $input['value'] ?? null, $inputLength, $input['required']);
    $this->validateArrayUnique($field, $input['value'] ?? null);

    foreach ($input['value'] ?? [] as $optionIndex => $option) {
      $optionField = $field.".{$optionIndex}.{$option}";
      $this->validateValueInOptions($optionField, [
        'value'    => $option,
        'options'  => $input['options'] ?? [],
        'required' => true
      ]);
    }
  }

  /**
   * Valida campos do tipo texto.
   * @param  string $field Campo atual sendo validado, usado para mensagens de erro.
   * @param  array  $input  Dados do campo a ser validado, incluindo tipo, valor e se é obrigatório.
   * @return void
   */
  private function validateText(string $field, array $input) :void {
    $maxLength = $input['type'] === 'text'
      ? FormFieldsLength::MAX_TEXT
      : FormFieldsLength::MAX_TEXTAREA;

    $this->validateString($field, $input['value'] ?? null, $maxLength, $input['required']);
  }

  /**
   * Valida campos do tipo número.
   * @param  string $field Campo atual sendo validado, usado para mensagens de erro.
   * @param  array  $input  Dados do campo a ser validado, incluindo tipo, valor e se é obrigatório.
   * @return void
   * @throws BusinessException Se o valor não for um número inteiro válido.
   */
  private function validateNumber(string $field, array $input) :void {
    $this->validateString($field, $input['value'] ?? null, isRequired: $input['required']);

    if(filter_var($input['value'], FILTER_VALIDATE_INT) === false)
      throw new BusinessException("O campo '{$field}' deve ser um número inteiro.", 400);
  }

  /**
   * Valida campos do tipo data.
   * @param  string $field Campo atual sendo validado, usado para mensagens de erro.
   * @param  array  $input  Dados do campo a ser validado, incluindo tipo, valor e se é obrigatório.
   * @return void
   * @throws BusinessException Se o valor não for uma data válida no formato 'YYYY-MM-DD'.
   */
  private function validateDate(string $field, array $input) :void { 
    $this->validateString($field, $input['value'] ?? null, isRequired: $input['required']);

    $date = \DateTime::createFromFormat('Y-m-d', $input['value']);

    if(!$date || $date->format('Y-m-d') !== $input['value'])
      throw new BusinessException("O campo '{$field}' deve ser uma data válida no formato 'YYYY-MM-DD'.", 400);
  }

  /**
   * Valida campos do tipo dropdown.
   * @param  string $field Campo atual sendo validado, usado para mensagens de erro.
   * @param  array  $input  Dados do campo a ser validado, incluindo tipo, valor, opções e se é obrigatório.
   * @return void
   * @throws BusinessException Se o valor não for uma string válida ou se não estiver entre as opções permitidas.
   */
  private function validateValueInOptions(string $field, array $input) :void {
    $this->validateString($field, $input['value'] ?? null, isRequired: $input['required']);

    if (!\in_array($input['value'], $input['options'] ?? [])) {
      throw new BusinessException("O campo '{$field}' contém uma opção inválida.", 400);
    }
  }

  /**
   * Valida a estrutura do formulário.
   * @return void
   * @throws BusinessException Se a estrutura do formulário enviado for diferente do formulário original, ignorando os valores dos campos.
   */
  private function validateFormStructure() :void {
    $diff = Functions::arrayDiffMultidimensional(
      $this->removeValueFromPayload($this->requestPayload), 
      $this->removeValueFromPayload($this->originalPayload)
    );

    if(\count($diff) > 0)
      throw new BusinessException("A estrutura do formulário enviado é diferente do formulário original.", 400);
  }

  /**
   * Método responsável por remover o campo value dos payloads
   * @param  array $payload Formulário do qual os valores devem ser removidos
   * @return array          Formulário com os valores removidos
   */
  private function removeValueFromPayload(array $payload) :array {
    if(!isset($payload['pages'][0]['inputs']) || !\is_array($payload['pages'][0]['inputs']))
      return $payload;

    $payload['pages'][0]['inputs'] = array_map(function($input) {
        if(\array_key_exists('value', $input)) 
          unset($input['value']);

        return $input;
    }, $payload['pages'][0]['inputs'] ?? []);

    return $payload;
  }

}