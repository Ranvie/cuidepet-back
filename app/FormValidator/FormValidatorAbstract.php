<?php

namespace App\FormValidator;

use App\Exceptions\BusinessException;

/**
 * Serviço abstrato de validação do formulário.
 */
abstract class FormValidatorAbstract {

  /**
   * Responsável por inicializar o processo de validação de formulários
   * @return string
   */
  abstract protected function resolve() :string; 

  /**
   * Validação de arrays
   * @param  string   $field      Nome do campo para mensagens de erro.
   * @param  mixed    $value      Valor a ser validado.
   * @param  int|null $length     Tamanho máximo permitido para o array.
   * @param  bool     $isRequired Indica se o campo é obrigatório ou não.
   * @return void
   * @throws BusinessException Caso o valor não seja um array, seja obrigatório e esteja vazio ou exceda o tamanho máximo permitido.
   */
  protected function validateArray(string $field, mixed $value, ?int $length = null, bool $isRequired = true) :void {
    if(!\is_array($value))
      throw new BusinessException("O campo '{$field}' deve estar presente e ser do tipo array.", 400);

    if($isRequired && \count($value) === 0)
      throw new BusinessException("O campo '{$field}' é obrigatório.", 400);

    if(\count($value) > $length && !is_null($length)){
      $length > 0
        ? throw new BusinessException("O campo '{$field}' deve conter no máximo uma lista de {$length} item(s).", 400)
        : throw new BusinessException("O campo '{$field}' deve ser um array vazio.", 400);
    }
  }

  /**
   * Validação de arrays únicos
   * @param string $field Nome do campo para mensagens de erro.
   * @param mixed  $value Valor a ser validado.
   * @return void
   */
  protected function validateArrayUnique(string $field, array $value) :void {
    if (\count($value) !== \count(array_unique($value)))
      throw new BusinessException("O campo '{$field}' contém valores duplicados.", 400);
  }

  /**
   * Validação de strings
   * @param string $field      Nome do campo para mensagens de erro.
   * @param mixed  $value      Valor a ser validado.
   * @param int    $length     Tamanho máximo permitido para a string.
   * @param bool   $isRequired Indica se o campo é obrigatório ou não.
   * @return void
   */
  protected function validateString(string $field, mixed $value, ?int $length = null, bool $isRequired = true) :void {
    if(!\is_string($value))
      throw new BusinessException("O campo '{$field}' deve estar presente e ser do tipo string.", 400);

    if($isRequired && \strlen(trim($value)) === 0)
      throw new BusinessException("O campo '{$field}' é obrigatório.", 400);

    if(\strlen($value) > $length && !is_null($length)){
      $length > 0 
        ? throw new BusinessException("O campo '{$field}' deve conter no máximo {$length} caractere(s).", 400)
        : throw new BusinessException("O campo '{$field}' deve ser uma string vazia", 400);
    }
  }

}