<?php

namespace App\DTO\AnnouncementMedia;
use App\DTO\Announcement\AnnouncementDTO;

class AnnouncementMediaDTO {
  public int $id;
  public AnnouncementDTO $announcement;
  public string $url;
}