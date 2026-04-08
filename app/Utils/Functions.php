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
}