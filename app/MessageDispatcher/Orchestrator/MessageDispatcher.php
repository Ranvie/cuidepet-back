<?php

namespace App\MessageDispatcher\Orchestrator;

use App\Exceptions\BusinessException;
use App\MessageDispatcher\Contracts\Builder;

/**
 * Classe que representa uma mensagem a ser processada pelo MessageDispatcher, contendo o tipo da mensagem e seu conteúdo
 */
class MessageDispatcher {

  /**
   * Construtor da classe
   * @param Builder $content Conteúdo da mensagem, que deve implementar a interface Builder e conter os dados necessários para o processamento
   */
  public function __construct(
    public Builder $content
  ) {}

  /**
   * Método para construir a mensagem, que pode ser implementado para realizar transformações ou validações antes do processamento
   * @return void
   */
  public function dispatch() :void {
    $sender = $this->content->getSender();
    
    try {
      $message = $this->content->build();
    } catch (BusinessException $e) {
      return;
    }

    $sender->send($message);
  }

}