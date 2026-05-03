<?php

namespace App\Services\Interfaces;

interface IFavoriteService {

  /**
   * Responsável por listar todos os anúncios favoritos do usuário
   * @param  int      $limit   Limite máximo de registros
   * @param  int      $page    Página atual da lista
   * @param  int|null $userId  ID do usuário cujos favoritos serão listados
   * @param  array    $filters Lista de filtros aplicados à listagem
   * @param  array    $orders  Lista de ordenação aplicados à listagem
   * @return array             Lista de anúncios favoritados
   */
  public function listFavorites(int $limit, int $page, ?int $userId, array $filters = [], array $orders = []) :array;

  /**
   * Responsável por possibilitar um usuário favoritar um anúncio
   * @param  int $userId         ID do usuário que deseja favoritar um anúncio
   * @param  int $announcementId ID do anúncio que deve ser favoritado
   * @return bool                Valor booleano indicando se o anúncio foi favoritado
   */
  public function addFavorite(int $userId, int $announcementId) :bool;

  /**
   * Responsável por remover um favorito do usuário
   * @param  int $userId         ID do usuário que deseja desfavoritar um anúncio
   * @param  int $announcementId ID do anúncio que deve ser desfavoritado
   * @return bool                Valor booleano indicando se o anúncio foi desfavoritado
   */
  public function removeFavorite(int $userId, int $announcementId) :bool;

}
