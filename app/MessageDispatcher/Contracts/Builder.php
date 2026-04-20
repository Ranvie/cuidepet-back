<?php

namespace App\MessageDispatcher\Contracts;

use App\Exceptions\BusinessException;

/**
 * Define o contrato para builders de mensagens
 */
interface Builder {
 
  /**
   * Constrói a mensagem com base nas configurações definidas no builder, realizando as transformações ou validações necessárias antes do envio
   * @return Message Retorna a mensagem construída, que deve implementar a interface Message e conter os dados necessários para o processamento
   * @throws BusinessException Se a validação falhar
   */
  public function build() :Message;

  /**
   * Retorna o sender associado a este builder, que será responsável por enviar a mensagem construída
   * @return Sender O sender associado a este builder
   */
  public function getSender() :Sender;
}
