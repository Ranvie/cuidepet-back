<?php

namespace App\Utils;

use Exception;

enum PARSE_MODE: int {
  case snakeToCamel = 0;
  case camelToSnake = 1;
}

class ParseConvention {

  /**
   * Converte a nomenclatura das chaves de um array ou objeto.
   * @param  array|object $origin O array ou objeto de origem que será convertido.
   * @param  PARSE_MODE   $mode Define o modo de conversão.
   * @param  string|null  $class Opcional. Se fornecido, o resultado será convertido para uma instância da classe especificada.
   * @return array|object Convertido para o formato desejado, podendo ser um array ou um objeto de uma classe específica.
   */
  public static function parse(array|object $origin, PARSE_MODE $mode = PARSE_MODE::camelToSnake, ?string $class = null): array|object {
    if($class !== null && !class_exists($class)) 
      throw new Exception("A classe '$class' não foi encontrada.");

    $parsed = [];

    if(\gettype($origin) === 'object') {
      $origin = get_object_vars($origin);
    }

    $parsed = match($mode) {
      PARSE_MODE::camelToSnake => self::parser($origin, 'camelToSnakeParser'),
      PARSE_MODE::snakeToCamel => self::parser($origin, 'snakeToCamelParser'),
    };

    if($class !== null) {
      $parsed = Objectfy::arrayToClass($parsed, $class);
    }

    return $parsed;
  }

  /**
   * Função recursiva para converter as chaves de um array usando o parser especificado.
   * @param  array  $origin O array de origem que será convertido.
   * @param  string $parser O nome da função de parser a ser usada para converter as chaves.
   * @return array O array convertido com as chaves modificadas de acordo com o parser.
   */
  private static function parser(array $origin, string $parser): array {
    $parsed    = [];
    $parsedKey = '';

    foreach ($origin as $key => $param) {
      if (\gettype($param) === 'array') {
        $parsedKey          = self::$parser($key);
        $parsed[$parsedKey] = self::parser($param, $parser);
        continue;
      }

      $parsedKey          = self::$parser($key);
      $parsed[$parsedKey] = $param;
    }

    return $parsed;
  }

  /** Função para converter uma string de camelCase para snake_case.
   * @param  string $text A string em camelCase a ser convertida.
   * @return string       A string convertida para snake_case.
   */
  private static function camelToSnakeParser(string $text): string {
    $result = '';

    $result = \gettype($text) === 'string' ? strtolower($text[0]) : $text;
    for ($i = 1; $i < \strlen($text); $i++) {
      if (ctype_upper($text[$i])) {
        $result .= '_' . strtolower($text[$i]);
      } else {
        $result .= $text[$i];
      }
    }

    return $result;
  }

  /** Função para converter uma string de snake_case para camelCase.
   * @param  string $text A string em snake_case a ser convertida.
   * @return string       A string convertida para camelCase.
   */
  private static function snakeToCamelParser(string $text): string {
    $result = '';

    $result = \gettype($text) === 'string' ? strtolower($text[0]) : $text;
    for ($i = 1; $i < \strlen($text); $i++) {
      if ($text[$i] === '_') {
        $i = $i < \strlen($text) ? $i + 1 : $i;

        $result .= strtoupper($text[$i]);
      } else {
        $result .= strtolower($text[$i]);
      }
    }

    return $result;
  }
}
