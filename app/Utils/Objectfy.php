<?php

namespace App\Utils;

use ReflectionClass;
use Exception;

//TODO: Tem como melhorar essa classe colocando para pegar as chaves por recursão... Ela só obtém no primeiro nível
class Objectfy {

  /**
   * Converte um array em uma classe (A classe deve ter um construtor que não recebe parâmetros)
   * @param array  $origin recebe um array com os atributos da classe
   * @param string $class  recebe a classe do objeto de retorno, ex Class::class
   * @return object|null Retorna um objeto do tipo da classe passada ou null caso a classe não exista
   * @throws Exception Lança uma exceção caso a classe não exista
   */
  public static function arrayToClass(array $origin, string $class): object | null {
    if(!class_exists($class))
      throw new Exception("A classe '$class' não foi encontrada");
  
    $parsed  = new $class();
    $refObj  = new ReflectionClass($parsed);
    $arrKeys = array_keys($origin);

    foreach ($arrKeys as $key) {
      if(!$refObj->hasProperty($key))
        continue;

      $prop = $refObj->getProperty($key);
      $prop->setValue($parsed, $origin[$key]);
    }

    return $parsed;
  }

  /** 
   * Transfere os atributos de um objeto ou array para outro objeto ou array
   * @param object|array $origin      O objeto ou array de origem
   * @param object|array $destin      O objeto ou array de destino
   * @param bool         $ignoreNulls Indica se os valores nulos devem ser ignorados (padrão: true)
   * @return object|array O objeto ou array de destino com os atributos transferidos
   */
  public static function transferTo($origin, $destin, $ignoreNulls = true) {
    $setter = \gettype($origin) === 'object' 
      ? 'setObjectValue' 
      : 'setArrayValue';

    foreach ($origin as $key => $value) {
      if($ignoreNulls && $value === null) 
        continue;

      $setter($destin, $key, $value);
    }

    return $destin;
  }

  /** 
   * Transfere os atributos de um objeto para outro objeto, ignorando os valores nulos
   * @param object $obj   O objeto de origem
   * @param object $key   A chave do objeto
   * @param mixed  $value Valor a ser atribuído
   */
  private static function setObjectValue(object &$obj, string $key, $value): void {
    $obj->$key = $value;
  }

  /** 
   * Transfere os atributos de um array para outro array
   * @param array  $arr   O array de destino
   * @param string $key   A chave do array
   * @param mixed  $value O valor a ser atribuído
   */
  private static function setArrayValue(array &$arr, string $key, $value): void {
    $arr[$key] = $value;
  }

}
