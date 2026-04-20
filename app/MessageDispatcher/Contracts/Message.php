<?php

namespace App\MessageDispatcher\Contracts;

/**
 * Define o contrato para mensagens que podem ser enviadas pelos senders, garantindo que elas implementem os métodos necessários para obter os destinatários, canais e conteúdo da mensagem
 */
interface Message {

  /**
   * Obtém os destinatários da mensagem
   * @return array Retorna um array de strings contendo os destinatários da mensagem, que podem ser endereços de email, IDs de usuários ou outros identificadores dependendo do tipo de mensagem e canais utilizados
   */
  public function getRecipients() :array;
  
}
