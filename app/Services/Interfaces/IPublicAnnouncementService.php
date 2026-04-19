<?php

namespace App\Services\Interfaces;

use App\DTO\Announcement\AnnouncementDTO;
use App\Exceptions\BusinessException;

/**
 * Interface para o serviço de anúncios públicos.
 * Define os métodos que devem ser implementados por qualquer classe que gerencie anúncios públicos.
 */
interface IPublicAnnouncementService {

  /**
   * Lista os anúncios públicos.
   * @param  int      $limit   Quantidade de anúncios por página.
   * @param  int      $page    Número da página.
   * @param  null|int $userId  ID do usuário que solicitou a lista
   * @param  array    $filters Filtros para a consulta dos anúncios.
   * @throws BusinessException Caso o tipo de anúncio seja inválido.
   * @return array             Lista de anúncios públicos.
   */
  public function getList(int $limit, int $page, ?int $userId = null, array $filters = [], array $orders = []) :array;
  
  /**
  * Obtém um anúncio público por ID.
  * @param  int $id              ID do anúncio.
   * @param  null|int $userId    ID do usuário que solicitou o anúncio
  * @param  array $relations     Relações a serem carregadas junto com o anúncio.
  * @return AnnouncementDTO|null Anúncio público ou null se não encontrado.
  */
  public function getById(int $id, ?int $userId = null, array $relations = []) :?AnnouncementDTO;
}
