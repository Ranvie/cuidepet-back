<?php

namespace App\FormValidator;

use App\Exceptions\BusinessException;
use App\Http\Enums\FormFieldsLength;
use App\Http\Enums\FormInputType;

/**
 * Serviço de validação do formulário.
 */
class FormStructureValidator extends FormValidatorAbstract {

  /**
   * Método Construtor
   * @param  array $requestPayload Formulário a ser validado.
   * @return void
   */
  public function __construct(
    private array $requestPayload,
  ) {}

  /**
   * Inicia processo de validação do formulário.
   * @return string
   */
  public function resolve() :string {

    // VALIDA O CAMPO 'pages' DO FORMULÁRIO
    $this->validateArray('pages', $this->requestPayload['pages'] ?? null, FormFieldsLength::MAX_PAGE);

    foreach($this->requestPayload['pages'] as $pageIndex => $page) {
      // VALIDA O CAMPO 'title' DE CADA PÁGINA
      $this->validateString("pages.{$pageIndex}.title", $page['title'] ?? null, FormFieldsLength::MAX_TITLE);

      // VALIDA O CAMPO 'inputs' DE CADA PÁGINA  
      $this->validateArray("pages.{$pageIndex}.inputs", $page['inputs'] ?? null, FormFieldsLength::MAX_INPUT);

      // VALIDA A ESTRUTURA DOS CAMPOS DO FORMULÁRIO
      $this->validateFormInputs($pageIndex, $page['inputs'] ?? []);
    }

    return $this->extractFormFields();
  }

  /**
   * Valida a estrutura dos campos do formulário.
   * @param  int   $pageIndex Posição da página no array de páginas do formulário.
   * @param  array $inputs    Array de inputs da página do formulário.
   * @return self
   */
  private function validateFormInputs(int $pageIndex, array $inputs) :self {
    foreach($inputs ?? [] as $inputIndex => $input) {
      $index = "pages.{$pageIndex}.inputs.{$inputIndex}.";

      // VALIDA O CAMPO 'title' DE CADA INPUT
      $this->validateString($index . "title", $input['title'] ?? null, FormFieldsLength::MAX_TITLE);

      // VALIDA O CAMPO 'type' DE CADA INPUT
      $this->validateInputType($index . "type", $input['type'] ?? null);

      // VALIDA O CAMPO 'placeholder' DE CADA INPUT
      $this->validateInputPlaceholder($index . "placeholder", $input['placeholder'] ?? null, $input['type']);

      // VALIDA O CAMPO 'options' DE CADA INPUT
      $this->validateInputOptions($index . "options", $input['options'] ?? null, $input['type']);

      // VALIDA O CAMPO 'required' DE CADA INPUT
      $this->validateInputRequired($index . "required", $input['required'] ?? null);

      // VALIDA O CAMPO 'value' DE CADA INPUT
      $this->validateInputValue($index . "value", $input['value'] ?? null, $input['type']);
    }

    return $this;
  }

  /**
   * Valida campo type do input
   * @param  string $field Campo do input a ser validado.
   * @param  mixed  $type  Valor do campo type do input a ser validado.
   * @return void
   * @throws BusinessException Caso o campo type do input não seja uma string ou não contenha um valor válido.  
   */
  private function validateInputType(string $field, mixed $type) :void {
    $this->validateString($field, $type);

    $validTypes = array_column(FormInputType::cases(), 'value');

    if(!\in_array($type, $validTypes))
      throw new BusinessException("O campo '{$field}' deve conter um valor entre (".implode(',', $validTypes).").", 422);
  }

  /**
   * Valida campo type do input
   * @param  string $field       Campo do input a ser validado.
   * @param  mixed  $placeholder Valor do campo placeholder do input a ser validado.
   * @param  string $type        Valor do campo type do input a ser validado.
   * @return void
   * @throws BusinessException Caso o campo placeholder do input seja preenchido para inputs do tipo checkbox ou radio
   */
  private function validateInputPlaceholder(string $field, mixed $placeholder, string $type) :void {
    $invalidTypes = [FormInputType::CHECKBOX->value, FormInputType::RADIO->value];
    if(\in_array($type, $invalidTypes) && !empty($placeholder))
      throw new BusinessException("O campo '{$field}' não pode conter um placeholder para inputs do tipo checkbox ou radio.", 422);

    if(!\in_array($type, $invalidTypes))
      $this->validateString($field, $placeholder, FormFieldsLength::MAX_PLACEHOLDER, isRequired: false);
  }

  /**
   * Responsável por validar o campo options dos inputs do tipo checkbox, radio e dropdown.
   * @param  string $field   Campo do input a ser validado.
   * @param  mixed  $options Valor do campo options do input a ser validado.
   * @param  string $type    Valor do campo type do input a ser validado.
   * @return void
   * @throws BusinessException Caso o campo options do input seja preenchido para inputs de tipos diferentes de checkbox
   */
  private function validateInputOptions(string $field, mixed $options, string $type) :void {
    $validTypes = [FormInputType::CHECKBOX->value, FormInputType::RADIO->value, FormInputType::DROPDOWN->value];

    if(\in_array($type, $validTypes))
      $this->validateArray($field, $options, FormFieldsLength::MAX_OPTIONS);
    else if(!empty($options))
      throw new BusinessException("O campo '{$field}' só pode conter opções para inputs do tipo checkbox, radio ou dropdown.", 422);

    foreach($options ?? [] as $optionIndex => $option) {
      $this->validateString("{$field}.{$optionIndex}", $option, FormFieldsLength::MAX_OPTION_VALUE);
    }
  }

  /**
   * Valida campo required do input
   * @param  string $field    Campo do input a ser validado.
   * @param  mixed  $required Valor do campo required do input a ser validado.
   * @return void
   * @throws BusinessException Caso o campo required do input não seja um valor booleano.
   */
  private function validateInputRequired(string $field, mixed $required) :void {
    if(!\is_bool($required))
      throw new BusinessException("O campo '{$field}' deve estar presente e conter um valor booleano.", 422);
  }

  /**
   * Valida o valor do input
   * @param  string $field Campo do input a ser validado.
   * @param  mixed  $value Valor do input a ser validado.
   * @param  string $type  Valor do campo type do input a ser validado.
   * @return void
   */
  private function validateInputValue(string $field, mixed $value, string $type) :void {
    match($type){
      'checkbox', 'radio' => $this->validateArray($field, $value, 0, false),
      default             => $this->validateString($field, $value, 0, false),
    };
  }

  /**
   * Valida a estrutura do formulário.
   * @return string
   */
  private function extractFormFields() :string {
    $this->requestPayload = [
      'pages' => array_map(function ($page) {
        return [
          'title'  => $page['title'] ?? '',
          'inputs' => array_map(fn ($input) => $this->getInputBodyByType($input), $page['inputs'] ?? [])
        ];
      }, $this->requestPayload['pages'] ?? [])
    ];

    return json_encode($this->requestPayload, JSON_UNESCAPED_UNICODE);
  }

  /**
   * Retorna estrutura do input baseado no tipo
   * @param  array $input Array do input a ser estruturado.
   * @return array
   */
  private function getInputBodyByType(array $input) :array {
    $body = [
      'title'    => $input['title']    ?? '',
      'type'     => $input['type']     ?? '',
      'value'    => $input['value']    ?? null,
      'required' => $input['required'] ?? false
    ];

    if(\in_array($input['type'], [FormInputType::CHECKBOX->value, FormInputType::RADIO->value, FormInputType::DROPDOWN->value]))
      $body['options'] = $input['options'] ?? [];

    if(!\in_array($input['type'], [FormInputType::CHECKBOX->value, FormInputType::RADIO->value]))
      $body['placeholder'] = $input['placeholder'] ?? '';

    return $body;
  }

}