<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator as FormValidator;
use \Illuminate\Contracts\Validation\Validator;

class UserFormRequest extends FormRequest {
  
  /**
   * Regras de validação para o formulário do usuário.
   * @return array
   */
  public function rules() :array {
    return [
      'title'   => 'required|string|max:255',
      'payload' => 'required|string|max:10000'
    ];
  }

  /**
   * Configura o validador para adicionar validações personalizadas após as regras básicas.
   * @param  Validator $validator Validador para adicionar erros de validação.
   * @return void
   */
  public function withValidator(Validator $validator): void {
    $validator->after(function (Validator $validator) {
      if ($validator->errors()->isNotEmpty())
        return;

      $payload = json_decode($this->input('payload'), true);

      $formValidator = FormValidator::make($payload ?? [], [
        'pages'                     => 'required|array|min:1|max:1',
        'pages.*.title'             => 'required|string|max:255',
        'pages.*.inputs'            => 'required|array|min:1|max:10',
        'pages.*.inputs.*.title'    => 'required|string|max:255',
        'pages.*.inputs.*.type'     => 'required|in:checkbox,textarea,number,radio,dropdown,text,date',
        'pages.*.inputs.*.options'  => 'required_if:pages.*.inputs.*.type,checkbox,radio,dropdown|array|min:1|max:10',
        'pages.*.inputs.*.value'    => 'present|nullable',
        'pages.*.inputs.*.required' => 'required|boolean'
      ]);

      if ($formValidator->fails()) {
        foreach ($formValidator->errors()->messages() as $field => $messages)
          foreach ($messages as $message)
            $validator->errors()->add($field, $message);

        return;
      }

      $this->validateFormInputs($validator, $payload['pages'] ?? []);
    });
  }

  /**
   * Realiza validação dos inputs do formulário, verificando se os valores estão de acordo com o tipo de entrada especificado.
   * @param  Validator $validator Validador para adicionar erros de validação.
   * @param  array     $pages     Páginas do formulário a serem validadas.
   * @return void
   */
  public function validateFormInputs(Validator $validator, array $pages) :void {
    foreach ($pages as $pageIndex => $page) {
      foreach ($page['inputs'] ?? [] as $inputIndex => $input) {
        $this->validateInputValue(
          $validator,
          $input,
          "pages.{$pageIndex}.inputs.{$inputIndex}.value"
        );
      }
    }
  }

  /**
   * Valida o valor do campo de entrada com base no tipo de entrada.
   * @param  Validator $validator Validador para adicionar erros de validação.
   * @param  array     $input     Dados do campo de entrada a ser validado.
   * @param  string    $field     Campo específico para o qual os erros devem ser adicionados.
   * @return void
   */
  private function validateInputValue(Validator $validator, array $input, string $field): void {
    $type  = $input['type']  ?? null;
    $value = $input['value'] ?? null;

    $arrayTypes = ['checkbox', 'radio'];
    \in_array($type, $arrayTypes)
      ? $this->validateArray($validator, $type, $field, $value)
      : $this->validateString($validator, $type, $field, $value);
  }

  /** 
   * Valida se o valor do campo de entrada é um array, dependendo do tipo de entrada.
   * @param  Validator $validator Validador para adicionar erros de validação.
   * @param  string    $type      Tipo de campo de entrada (checkbox, radio, dropdown, text, date).
   * @param  string    $field     Campo específico para o qual os erros devem ser adicionados.
   * @param  mixed     $value     Valor do campo de entrada a ser validado.
   * @return void
   */
  private function validateArray(Validator $validator, string $type, string $field, mixed $value) {
    if(!\is_array($value))
      $validator->errors()->add($field, "O campo value deve ser um array para o tipo {$type}.");
  }

  /** 
   * Valida se o valor do campo de entrada é uma string não vazia, dependendo do tipo de entrada.
   * @param  Validator $validator Validador para adicionar erros de validação.
   * @param  string    $type      Tipo de campo de entrada (checkbox, radio, dropdown, text, date).
   * @param  string    $field     Campo específico para o qual os erros devem ser adicionados.
   * @param  mixed     $value     Valor do campo de entrada a ser validado.
   * @return void
   */
  private function validateString(Validator $validator, string $type, string $field, mixed $value) {
    if(!\is_string($value) || \strlen($value) > 255)
      $validator->errors()->add($field, "O campo value deve ser uma string com no máximo 255 caracteres para o tipo {$type}.");
  }
}
