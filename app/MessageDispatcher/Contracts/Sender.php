<?php

namespace App\MessageDispatcher\Contracts;

/**
 * Define o contrato para os senders, que são responsáveis por enviar as mensagens utilizando os canais apropriados, garantindo que eles implementem o método de envio
 */
interface Sender {

  /**
   * Envia a mensagem utilizando o canal suportado pelo sender
   * @param  Message $message A mensagem a ser enviada, que deve ser compatível com o canal suportado pelo sender
   * @return void             Não retorna nenhum valor, apenas executa a ação de envio da mensagem
   */
  public function send(Message $message) :void;
}
