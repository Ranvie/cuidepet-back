<?php

namespace App\Utils;

/**
 * Classe utilitária para validação de campos obrigatórios em arrays.
 */
class RequiredFieldsValidator {
  
  /**
   * Valida se os campos obrigatórios estão presentes na response, suportando dot notation para campos aninhados.
   * Considerados como inválidos os campos que estão: ausentes, nulos (null), vazios ('') ou arrays vazios ([]).
   * @param  array $response       Dados da response a serem validados.
   * @param  array $requiredFields Campos obrigatórios a serem verificados (ex: 'location.coordinates.*.latitude').
   * @return bool                  Retorna true se todos os campos obrigatórios forem válidos, caso contrário, false.
   */
  public static function validate(array $response, array $requiredFields): bool {
    foreach ($requiredFields as $field) {
      if (!self::resolve($response, explode('.', $field)))
        return false;
    }

    return true;
  }

  /**
   * Valida se os campos obrigatórios estão presentes na response, suportando dot notation para campos aninhados.
   * @param  array|string $data  Dados a serem validados.
   * @param  array        $keys  Campos obrigatórios a serem verificados (ex: 'location.coordinates.*.latitude').
   * @param  int          $index Índice atual para verificação dos campos. Não deve ser informado, utilizado internamente.
   * @param  int          $total Total de campos a serem verificados. Não deve ser informado, utilizado internamente.
   * @return bool                Retorna true se todos os campos obrigatórios forem válidos, caso contrário, false.
   */
  private static function resolve(mixed $data, array $keys, int $index = 0, int $total = -1): bool {
    if ($total === -1)
      $total = \count($keys);

    if ($index === $total)
      return $data !== null && $data !== '' && !(\is_array($data) && $data === []);

    $key  = $keys[$index];
    $next = $index + 1;

    if ($key === '*') {
      if (!\is_array($data) || $data === [])
        return false;

      foreach ($data as $item) {
        if (!self::resolve($item, $keys, $next, $total))
          return false;
      }

      return true;
    }

    if (!\is_array($data) || !\array_key_exists($key, $data))
      return false;

    return self::resolve($data[$key], $keys, $next, $total);
  }
}