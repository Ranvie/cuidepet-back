<?php

namespace App\DTO\ReportMessage;

class ReportMessageDTO {

  /**
   * Identificador da mensagem do relatório
   * @var int
   */
  public int $id;

  /**
   * Motivo do relatório
   * @var string
   */
  public string $motive;

  /**
   * Tipo do relatório
   * @var string
   */
  public string $type;
}