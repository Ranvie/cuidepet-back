<?php

namespace App\DTO\User;

use App\DTO\Notification\NotificationDTO;

class SafeUserDTO{
  public int $id;
  public string $username;
  public string $imageProfile;
  public NotificationDTO $notifications;
}