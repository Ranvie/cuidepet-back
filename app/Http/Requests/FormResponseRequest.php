<?php

namespace App\Http\Requests;

use App\Services\FormService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator as FormValidator;
use \Illuminate\Contracts\Validation\Validator;

/**
 * Classe de requisição para validação de respostas de formulários.
 * Define as regras de validação para a criação de respostas de formulários, incluindo a estrutura do payload e a correspondência com o formulário original.
 */
class FormResponseRequest extends FormRequest {

  /**
   * Método Construtor
   * @param FormService $formService Serviço para manipulação de formulários, injetado via dependência.
   * @return void
   */
  public function __construct(
    private FormService $formService
  ) {}
  
  /**
   * Regras de validação para o formulário do usuário.
   * @return array
   */
  public function rules() :array {
    return [
      'announcementId' => 'required|integer',
      'payload'        => 'required|string|max:10000'
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

      $requestPayload  = $this->getRequestPayload();
      $originalPayload = $this->getOriginalFormData();

      $diff = $this->arrayDiffMultidimensional($requestPayload, $originalPayload);

      if(count($diff) > 0){
        $validator->errors()->add('payload', 'O payload enviado contém campos que não existem no formulário original ou foram modificados.');
        return;
      }

      $formValidator = FormValidator::make($requestPayload ?? [], [
        'pages'                     => 'required|array|min:1|max:1',
        'pages.*.inputs'            => 'required|array|min:1|max:10',
        'pages.*.inputs.*.type'     => 'required|in:checkbox,textarea,number,radio,dropdown,text,date',
        'pages.*.inputs.*.value'    => 'required_if:pages.*.inputs.*.required,true',
        'pages.*.inputs.*.required' => 'required|boolean'
      ]);

      if ($formValidator->fails()) {
        foreach ($formValidator->errors()->messages() as $field => $messages)
          foreach ($messages as $message)
            $validator->errors()->add($field, $message);

        return;
      }

      $this->validateFormInputs($validator, $requestPayload['pages'] ?? [], $originalPayload['pages'] ?? []);
    });
  }

  /**
   * Busca o formulário original do anúncio
   * @return array
   */
  private function getOriginalFormData() :array {
    $announcementId  = $this->input('announcementId');
    $obFormDTO       = $this->formService->getFormByAnnouncement($announcementId);
    $originalPayload = json_decode($obFormDTO->payload, true);
    
    return $this->RemoveFieldsFromPayload($originalPayload);
  }

  /**
   * Método responsável por buscar o payload da request
   * @return array
   */
  private function getRequestPayload() :array {
    $payload = json_decode($this->input('payload'), true);
    return $this->removeFieldsFromPayload($payload);
  }

  /**
   * Método responsável por remover o campo value dos payloads
   * @param  array $payload
   * @return array
   */
  private function removeFieldsFromPayload(array $payload) :array {
    foreach($payload['pages'][0]['inputs'] ?? [] as &$input){
      unset($input['value']);
    }

    return $payload;
  }

  /**
   * Realiza validação dos inputs do formulário, verificando se os valores estão de acordo com o tipo de entrada especificado.
   * @param  Validator $validator Validador para adicionar erros de validação.
   * @param  array     $pages     Páginas do formulário a serem validadas.
   * @return void
   */
  public function validateFormInputs(Validator $validator, array $pages, array $originalPages) :void {
    foreach ($pages as $pageIndex => $page) {
      foreach ($page['inputs'] ?? [] as $inputIndex => $input) {
        $this->validateInputValue(
          $validator,
          $input,
          "pages.{$pageIndex}.inputs.{$inputIndex}.value",
          $originalPages[$pageIndex]['inputs'][$inputIndex] ?? []
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
  private function validateInputValue(Validator $validator, array $input, string $field, array $originalInput): void {
    $type  = $input['type']  ?? null;
    $value = $input['value'] ?? null;

    $arrayTypes = ['checkbox', 'radio'];
    \in_array($type, $arrayTypes)
      ? $this->validateArray($validator, $type, $field, $value, $originalInput)
      : $this->validateString($validator, $type, $field, $value, $originalInput);
  }

  /** 
   * Valida se o valor do campo de entrada é um array, dependendo do tipo de entrada.
   * @param  Validator $validator Validador para adicionar erros de validação.
   * @param  string    $type      Tipo de campo de entrada (checkbox, radio, dropdown, text, date).
   * @param  string    $field     Campo específico para o qual os erros devem ser adicionados.
   * @param  mixed     $value     Valor do campo de entrada a ser validado.
   * @return void
   */
  private function validateArray(Validator $validator, string $type, string $field, mixed $value, array $originalInput) {
    if(!\is_array($value))
      $validator->errors()->add($field, "O campo value deve ser um array para o tipo {$type}.");

    $originalOptions = $originalInput['options'] ?? [];

    foreach ($value as $option) {
      if (!\is_string($option) || \strlen($option) > 255)
        $validator->errors()->add($field, "Cada opção do campo value deve ser uma string com no máximo 255 caracteres para o tipo {$type}.");

      if (!in_array($option, $originalOptions))
        $validator->errors()->add($field, "A opção '{$option}' não existe nas opções originais para o tipo {$type}.");
    }
  }

  /** 
   * Valida se o valor do campo de entrada é uma string não vazia, dependendo do tipo de entrada.
   * @param  Validator $validator Validador para adicionar erros de validação.
   * @param  string    $type      Tipo de campo de entrada (checkbox, radio, dropdown, text, date).
   * @param  string    $field     Campo específico para o qual os erros devem ser adicionados.
   * @param  mixed     $value     Valor do campo de entrada a ser validado.
   * @return void
   */
  private function validateString(Validator $validator, string $type, string $field, mixed $value, array $originalInput) {
    if(!\is_string($value) || \strlen($value) > 255)
      $validator->errors()->add($field, "O campo value deve ser uma string com no máximo 255 caracteres para o tipo {$type}.");
  
    $originalOptions = $originalInput['options'] ?? [];

    if ($type === 'dropdown' && !in_array($value, $originalOptions))
      $validator->errors()->add($field, "A opção '{$value}' não existe nas opções originais para o tipo {$type}.");
    
    if ($type === 'date' && !\DateTime::createFromFormat('Y-m-d', $value))
      $validator->errors()->add($field, "O campo value deve ser uma data válida no formato YYYY-MM-DD para o tipo {$type}.");
  }

  /**
   * Compara dois arrays multidimensionais e retorna as diferenças.
   * Compara recursivamente arrays aninhados e retorna elementos do primeiro array 
   * que não existem no segundo array ou são diferentes.
   * @param  array $array1 Primeiro array para comparação
   * @param  array $array2 Segundo array para comparação
   * @return array         Array contendo as diferenças encontradas
   */
  private function arrayDiffMultidimensional(array $array1, array $array2): array {
    $diff = [];

    foreach ($array1 as $key => $value) {
      // Se a chave não existe no segundo array, adiciona ao diff
      if (!\array_key_exists($key, $array2)) {
        $diff[$key] = $value;
        continue;
      }

      // Se ambos os valores são arrays, compara recursivamente
      if (\is_array($value) && \is_array($array2[$key])) {
        $recursiveDiff = $this->arrayDiffMultidimensional($value, $array2[$key]);
        
        // Apenas adiciona ao diff se houver diferenças
        if (!empty($recursiveDiff)) {
          $diff[$key] = $recursiveDiff;
        }
      } 
      // Se os valores são diferentes (não-arrays ou arrays vs não-arrays)
      else if ($value !== $array2[$key]) {
        $diff[$key] = $value;
      }
    }

    return $diff;
  }  
}
