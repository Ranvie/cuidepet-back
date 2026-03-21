<?php

namespace App\DTO\Notification;

use App\DTO\NotificationTemplate\NotificationTemplateDTO;
use App\DTO\User\UserDTO;

class NotificationDTO {
  public int $id;
  public UserDTO $user;
  public NotificationTemplateDTO $notificationTemplate;
  public bool $viewed;
  public string $createdAt;
}
