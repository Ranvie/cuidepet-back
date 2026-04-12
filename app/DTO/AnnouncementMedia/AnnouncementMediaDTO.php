<?php

namespace App\DTO\AnnouncementMedia;
use App\DTO\Announcement\AnnouncementDTO;

class AnnouncementMediaDTO {

  /**
   * Identificador da mídia do anúncio
   * @var int
   */
  public int $id;

  /**
   * Objeto do anúncio vinculado à mídia
   * @var AnnouncementDTO
   */
  public AnnouncementDTO $announcement;

  /**
   * URL da mídia
   * @var string
   */
  public string $url;

  /**
   * URL completa da mídia
   * @var string|null
   */
  public ?string $imageUrl;
}