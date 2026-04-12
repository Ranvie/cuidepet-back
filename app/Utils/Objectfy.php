<?php

namespace App\Utils;

use ReflectionClass;
use Exception;

class Objectfy {

  /**
   * Converte um array em uma classe de forma recursiva.
   * Subclasses e arrays de subclasses são resolvidos automaticamente a partir do @var do PHPDoc.
   *
   * Para propriedades aninhadas, documente com:
   * @var ClassName   preenche um objeto aninhado
   * @var ClassName[] preenche um array de objetos
   *
   * A classe (e subclasses) devem ter um construtor que não recebe parâmetros.
   *
   * @param  array  $origin recebe um array com os atributos da classe
   * @param  string $class  recebe a classe do objeto de retorno, ex Class::class
   * @return object|null    Retorna um objeto do tipo da classe passada ou null caso a classe não exista
   * @throws Exception      Lança uma exceção caso a classe não exista
   */
  public static function arrayToClass(array $origin, string $class): object | null {
    if(!class_exists($class))
      throw new Exception("A classe '$class' não foi encontrada");

    $parsed    = new $class();
    $refObj    = new ReflectionClass($parsed);
    $namespace = $refObj->getNamespaceName();

    foreach (array_keys($origin) as $key) {
      if(!$refObj->hasProperty($key))
        continue;

      $prop  = $refObj->getProperty($key);
      $value = $origin[$key];

      $varType = self::extractVarType($prop->getDocComment() ?: '');

      if($varType !== null && \is_array($value)) {
        $isList      = str_ends_with($varType, '[]');
        $targetClass = $isList ? substr($varType, 0, -2) : $varType;
        $resolved    = self::resolveClass($targetClass, $namespace);

        if($resolved !== null) {
          $value = $isList
            ? array_map(fn($item) => \is_array($item) ? self::arrayToClass($item, $resolved) : $item, $value)
            : self::arrayToClass($value, $resolved);
        }
      }

      $prop->setValue($parsed, $value);
    }

    return $parsed;
  }

  /**
   * Extrai o tipo declarado no @var do bloco PHPDoc de uma propriedade.
   * @param string $docComment
   * @return string|null
   */
  private static function extractVarType(string $docComment): ?string {
    if(preg_match('/@var\s+(\S+)/', $docComment, $matches))
      return $matches[1];

    return null;
  }

  /**
   * Tenta resolver o nome de uma classe dentro de um namespace.
   * Testa primeiro com o namespace da classe pai, depois como nome global.
   * @param  string $className
   * @param  string $namespace
   * @return string|null Fully qualified class name ou null se não encontrada
   */
  private static function resolveClass(string $className, string $namespace): ?string {
    $candidates = [
      $namespace . '\\' . $className,
      $className,
    ];

    foreach($candidates as $candidate) {
      if(class_exists($candidate))
        return $candidate;
    }

    return null;
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

      self::$setter($destin, $key, $value);
    }

    return $destin;
  }

  /** 
   * Transfere os atributos de um objeto para outro objeto
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
