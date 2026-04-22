<?php

namespace App\DTO\Favorites;

/**
 * Data Transfer Object (DTO) para representar um favorito de anúncio.
 * Contém informações sobre o usuário que favoritou e o anúncio favoritado.
 */
class FavoriteDTO {

  /**
   * Identificador do formulário
   * @var int
   */
  public int $id;
  
  /**
   * ID do usuário que favoritou
   * @var int
   */
  public int $userId;

  /**
   * ID do anúncio favoritado
   * @var int
   */
  public int $announcementId;

}