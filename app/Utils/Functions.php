<?php

namespace App\Utils;

/**
 * Classe de funções utilitárias para manipulação de strings e validação de dados.
 */
class Functions {

  /**
   * Remove caracteres não numéricos de uma string.
   * @param  string $text String a ser processada.
   * @return string       String contendo apenas números.
   */
  public static function getNumbersOnly(string $text) :string {
    return preg_replace('/\D/', '', $text);
  }

  /**
   * Remove caracteres que não sejam letras de uma string.
   * @param  string $text String a ser processada.
   * @return string       String contendo apenas letras.
   */
  public static function getOnlyLetters(string $text) :string {
    return preg_replace('/[^a-zA-Z]/', '', $text);
  }

  /**
   * Verifica se o CEP é válido.
   * @param  string $zipCode CEP a ser validado.
   * @return bool            Retorna true se o CEP for válido, caso contrário, false.
   */
  public static function isZipCodeValid(string $zipCode) :bool {
    return preg_match('/^\d{5}-?\d{3}$/', $zipCode) === 1;
  }

   /**
   * Compara dois arrays multidimensionais e retorna as diferenças.
   * Compara recursivamente arrays aninhados e retorna elementos do primeiro array 
   * que não existem no segundo array ou são diferentes.
   * @param  array $array1 Primeiro array para comparação
   * @param  array $array2 Segundo array para comparação
   * @return array         Array contendo as diferenças encontradas
   */
  public static function arrayDiffMultidimensional(array $array1, array $array2): array {
    $diff = [];

    foreach ($array1 as $key => $value) {
      // Se a chave não existe no segundo array, adiciona ao diff
      if (!\array_key_exists($key, $array2)) {
        $diff[$key] = $value;
        continue;
      }

      // Se ambos os valores são arrays, compara recursivamente
      if (\is_array($value) && \is_array($array2[$key])) {
        $recursiveDiff = self::arrayDiffMultidimensional($value, $array2[$key]);
        
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

  /**
   * Gera um hash aleatório de uma determinada extensão.
   * @param  int    $length Extensão do hash a ser gerado.
   * @return string         Hash aleatório gerado, com extensão de 2 vezes o valor de $length (devido ao uso de bin2hex).
   */
  public static function getRandomHash(int $length = 32) :string {
    return bin2hex(random_bytes($length));
  }
}