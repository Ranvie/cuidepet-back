<?php

namespace App\Services\Interfaces;

use App\DTO\Announcement\AnnouncementDTO;

interface IPublicAnnouncementService {

  /**
   * Lista os anúncios públicos.
   * @param  int $limit   Quantidade de anúncios por página.
   * @param  int $page    Número da página.
   * @param  string $type Tipo de anúncio.
   * @return array        Lista de anúncios públicos.
   */
  public function getList(int $limit, int $page, string $type) :array;
  
  /**
  * Obtém um anúncio público por ID.
  * @param  int $id              ID do anúncio.
  * @param  array $relations     Relações a serem carregadas junto com o anúncio.
  * @return AnnouncementDTO|null Anúncio público ou null se não encontrado.
  */
  public function getById(int $id, array $relations = []) :?AnnouncementDTO;
}
