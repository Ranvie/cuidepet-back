<?php

namespace App\DTO\Report;

use App\DTO\Announcement\AnnouncementDTO;
use App\DTO\Form\FormDTO;
use App\DTO\ReportMessage\ReportMessageDTO;
use App\DTO\User\UserDTO;

class ReportDTO {
  public int $id;
  public UserDTO $user;
  public ReportMessageDTO $reportMessage;
  public ?AnnouncementDTO $announcement;
  public ?FormDTO $form;
  public string $description;
  public string $createdAt;
}
