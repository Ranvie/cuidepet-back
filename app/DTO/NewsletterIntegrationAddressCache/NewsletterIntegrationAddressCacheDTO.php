<?php

namespace App\DTO\NewsletterIntegrationAddressCache;

/**
 * DTO para representar a associação entre uma newsletter e um cache de endereço
 */
class NewsletterIntegrationAddressCacheDTO {

  /**
   * Identificador do cache de integração
   * @var int
   */
  public int $id;

  /**
   * ID da newsletter associada
   * @var int
   */
  public int $newsletterId;

  /**
   * ID do cache de endereço
   * @var int
   */
  public int $addressCacheId;

  /**
   * Hash único para identificação da associação
   * @var string
   */
  public string $hash;

}