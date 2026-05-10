<?php

namespace App\Traits;

/**
 * Trait para manipulação de dados em requisições de formulários.
 * Permite a conversão de valores específicos para null, facilitando o tratamento de campos opcionais.
 */
trait FormDataRequest {

  /**
   * Valor específico que, quando recebido, será convertido para null.
   * @var string
   */
  private const string NULL_VALUE = '__NULL__';

   /**
   * Sobrescreve o método all para converter valores específicos em null.
   * Se um valor for igual a __NULL__, ele será convertido para null.
   * Isso é útil para lidar com campos opcionais que podem ser enviados como string "__NULL__" para indicar ausência de valor.
   * @param  array|null $keys
   * @return mixed
   */
  public function all(?array $keys = null) :mixed {
    $data = $keys === null ? parent::all() : $keys;

    foreach($data as $key => $value) {
      if(\is_object($value) || \is_array($value)) {
        $data[$key] = $this->all($data[$key]);
      } else if ($value === self::NULL_VALUE) {
        $data[$key] = null;
      }
    }

    return $data;
  }
}