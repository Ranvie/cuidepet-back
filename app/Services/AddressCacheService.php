<?php

namespace App\Services;
use App\Models\IntegrationAddressCacheModel;
use App\DTO\IntegrationAddressCache\IntegrationAddressCacheDTO;
use App\Exceptions\BusinessException;
use App\ExternalAPI\Address\Provider\ExternalAddressProvider;

/**
 * Serviço de cache de endereços, responsável por gerenciar o cache de endereços e resolver endereços a partir de serviços externos quando necessário.
 */
class AddressCacheService {

  /**
   * Construtor do serviço de cache de endereços.
   * @param IntegrationAddressCacheModel $obIntegrationAddressCacheModel Modelo de cache de endereços.
   */
  public function __construct(
    private IntegrationAddressCacheModel $obIntegrationAddressCacheModel,
    private ExternalAddressProvider $externalAddressProvider
  ) {}

  /**
   * Obtém um cache de endereço por CEP.
   * @param  string $zipCode CEP do endereço a ser obtido.
   * @return IntegrationAddressCacheDTO Objeto de transferência de dados do cache de endereço.
   */
  public function getByZipCode(string $zipCode) :IntegrationAddressCacheDTO {
    $addressCache = $this->obIntegrationAddressCacheModel->where('zipcode', $zipCode)->first();
    $expired      = $this->isCacheExpired($addressCache);
    if (!$addressCache instanceof IntegrationAddressCacheDTO || $expired) {
      $addressCache = $this->externalAddressProvider->resolve($zipCode);
      
      $this->validateCache($addressCache, $zipCode);
      
      $expired
        ? $this->obIntegrationAddressCacheModel->where('zipcode', $zipCode)->update((array)$addressCache) 
        : $this->obIntegrationAddressCacheModel->create((array)$addressCache); //TODO: Tem que fazer o POINT na hora de inserir/atualizar..
      
      return $addressCache;
    }
    
    return $addressCache;
  }

  /**
   * Valida um cache de endereço.
   * @param  IntegrationAddressCacheDTO $addressCache Cache de endereço a ser validado.
   * @param  string                     $zipCode      CEP do endereço para validação.
   * @return bool                                     Verdadeiro se o cache for válido, falso caso contrário.
   * @throws BusinessException                        Exceção lançada se o cache for inválido.
   */
  private function validateCache(IntegrationAddressCacheDTO $addressCache, string $zipCode) :bool {
    if(!$addressCache instanceof IntegrationAddressCacheDTO)
      throw new BusinessException("Não foi possível obter o endereço para o CEP: $zipCode, verifique se o CEP está correto.", 404);

    return true;
  }

  /**
   * Verifica se um cache de endereço está expirado.
   * @param  IntegrationAddressCacheDTO $addressCache Cache de endereço a ser verificado.
   * @return bool                       Verdadeiro se o cache estiver expirado, falso caso contrário.
   */
  private function isCacheExpired(IntegrationAddressCacheDTO $addressCache) :bool {
    if(!$addressCache instanceof IntegrationAddressCacheDTO)
      return false;

    $cacheTime   = strtotime($addressCache->expiresAt);
    $currentTime = time();

    // Considera o cache expirado se tiver mais de 24 horas (86400 segundos)
    return $currentTime > $cacheTime;
  }

}