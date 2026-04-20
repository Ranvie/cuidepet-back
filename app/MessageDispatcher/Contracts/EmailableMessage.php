<?php

namespace App\MessageDispatcher\Contracts;

/**
 * Interface para mensagens de email, definindo os métodos necessários para obter o assunto, template e dados do email
 */
interface EmailableMessage {

  /**
   * Obtém o assunto do email
   * @return string Retorna o assunto do email
   */
  public function getSubject() :string;

  /**
   * Obtém o template markdown do email
   * @return string Retorna o nome do template markdown a ser utilizado para renderizar o email
   */
  public function getTemplate() :string;

  /**
   * Obtém os dados para o template do email
   * @return array Retorna um array associativo contendo os dados para o template do email, que podem ser utilizados para renderizar o conteúdo do email
   */
  public function getData() :array;

  /**
   * Obtém os anexos para o email
   * @return array Retorna um array de strings contendo os caminhos dos arquivos a serem anexados ao email, ou um array de arrays associativos contendo os dados dos arquivos a serem anexados (ex: ['path' => 'caminho/do/arquivo', 'name' => 'nome_do_arquivo.ext'])
   */
  public function getAttachments() :array;
}
