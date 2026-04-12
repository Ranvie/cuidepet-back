<?php

namespace App\Utils;

/**
 * Classe utilitária para operações relacionadas a URLs.
 * Fornece métodos para manipulação e construção de URLs, especialmente para arquivos de mídia.
 */
class Url {

  /**
   * Base URL da aplicação, definida a partir do nome da aplicação.
   * @var string $baseUrl
   */
  protected string $baseUrl;

  /**
   * Construtor da classe Url.
   * Define a base URL da aplicação usando o nome da aplicação configurado.
   */
  public function __construct() {
    $this->baseUrl = config('app.url_port') . '/api/';
  }

  /**
   * Seletor para configurar a base URL da aplicação.
   * @param  string $resource Nova URL base a ser configurada.
   * @return self             Retorna a instância atual para permitir encadeamento de métodos.
   */
  public function setResource(string $resource) :self {
    match ($resource) {
      'media' => $this->baseUrl .= 'storage/',
    };

    return $this;
  }

  /**
    * Método para obter o caminho completo de um arquivo de mídia a partir de sua URL.
    * @param  string $url URL do arquivo de mídia.
    * @return string      Caminho completo do arquivo de mídia.
    */
  public function getMediaUrlPath(string $url) :string {
    return $this->baseUrl . $url;
  }

}