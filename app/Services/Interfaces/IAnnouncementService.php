<?php

namespace App\Services\Interfaces;

use App\DTO\Announcement\AnnouncementDTO;
use App\Exceptions\BusinessException;

interface IAnnouncementService extends IService {

  /**
   * Busca o anúncio associado a um formulário específico de um usuário.
   * @param  int $userId         ID do usuário.
   * @param  int $announcementId ID do anúncio.
   * @return AnnouncementDTO     Anúncio associado ao formulário do usuário.
   * @throws BusinessException   Se o anúncio não for encontrado ou não pertencer ao usuário.
   */
  public function getUserAnnouncement(int $userId, int $announcementId) :AnnouncementDTO;

}
