<?php

namespace App\DTO\Report;

use App\DTO\Announcement\AnnouncementDTO;
use App\DTO\Form\FormDTO;
use App\DTO\ReportMessage\ReportMessageDTO;
use App\DTO\User\UserDTO;

class ReportDTO {

  /**
   * Identificador do relatório
   * @var int
   */
  public int $id;

  /**
   * Objeto do usuário que gerou o relatório
   * @var UserDTO
   */
  public UserDTO $user;

  /**
   * Objeto da mensagem do relatório
   * @var ReportMessageDTO
   */
  public ReportMessageDTO $reportMessage;

  /**
   * Objeto do anúncio reportado
   * @var AnnouncementDTO|null
   */
  public ?AnnouncementDTO $announcement;

  /**
   * Objeto do formulário reportado
   * @var FormDTO|null
   */
  public ?FormDTO $form;

  /**
   * Descrição do relatório
   * @var string
   */
  public string $description;

  /**
   * Data de criação do relatório
   * @var string
   */
  public string $createdAt;
}
