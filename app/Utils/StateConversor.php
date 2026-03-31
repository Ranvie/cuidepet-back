<?php

namespace App\Utils;

/**
 * Classe utilitária para conversão de estados brasileiros entre siglas e nomes completos.
 * Esta classe fornece métodos para obter a sigla de um estado a partir do seu nome completo, 
 * obter o nome completo a partir da sigla e verificar se uma sigla é válida.
 */
class StateConversor {
  
  /**
   * Mapeamento dos estados brasileiros, onde a chave é a sigla e o valor é o nome completo do estado.
   * @var array
   */
  private static array $states = [
    'AC' => 'Acre',
    'AL' => 'Alagoas',
    'AP' => 'Amapá',
    'AM' => 'Amazonas',
    'BA' => 'Bahia',
    'CE' => 'Ceará',
    'DF' => 'Distrito Federal',
    'ES' => 'Espírito Santo',
    'GO' => 'Goiás',
    'MA' => 'Maranhão',
    'MT' => 'Mato Grosso',
    'MS' => 'Mato Grosso do Sul',
    'MG' => 'Minas Gerais',
    'PA' => 'Pará',
    'PB' => 'Paraíba',
    'PR' => 'Paraná',
    'PE' => 'Pernambuco',
    'PI' => 'Piauí',
    'RJ' => 'Rio de Janeiro',
    'RN' => 'Rio Grande do Norte',
    'RS' => 'Rio Grande do Sul',
    'RO' => 'Rondônia',
    'RR' => 'Roraima',
    'SC' => 'Santa Catarina',
    'SP' => 'São Paulo',
    'SE' => 'Sergipe',
    'TO' => 'Tocantins'
  ];

  /**
   * Converte o nome completo de um estado para sua sigla correspondente.
   * @param  string      $stateName Nome completo do estado a ser convertido.
   * @return string|null            Sigla do estado correspondente ou null se o estado não for encontrado.
   */
  public static function getStateAbbreviation(string $stateName) :?string {
    $stateName = trim($stateName);
    
    foreach (self::$states as $abbreviation => $name) {
      if (strcasecmp($name, $stateName) === 0) {
        return $abbreviation;
      }
    }

    return null;
  }

  /**
   * Converte a sigla de um estado para seu nome completo correspondente.
   * @param  string      $stateAbbreviation Sigla do estado a ser convertido.
   * @return string|null                    Nome completo do estado correspondente ou null se a sigla não for encontrada.
   */  
  public static function getStateName(string $stateAbbreviation): ?string {
    $stateAbbreviation = strtoupper(trim($stateAbbreviation));

    return self::$states[$stateAbbreviation] ?? null;
  }

  /**
   * Verifica se uma sigla de estado é válida.
   * @param  string $stateAbbreviation Sigla do estado a ser verificada.
   * @return bool                      Verdadeiro se a sigla for válida, falso caso contrário.
   */
  public static function isStateAbbreviation(string $stateAbbreviation) :bool {
    return array_key_exists(strtoupper(trim($stateAbbreviation)), self::$states);
  }
}