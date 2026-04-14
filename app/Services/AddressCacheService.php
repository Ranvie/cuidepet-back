<?php

namespace App\Services;

use App\Classes\Filter;
use App\Models\IntegrationAddressCacheModel;
use App\DTO\IntegrationAddressCache\IntegrationAddressCacheDTO;
use App\Exceptions\BusinessException;
use App\ExternalAPI\Address\Provider\ExternalAddressProvider;
use \App\Utils\Functions;

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
    private ExternalAddressProvider      $externalAddressProvider
  ) {}

  /**
   * Obtém um cache de endereço por ID.
   * @param  int $id                    ID do cache de endereço a ser obtido.
   * @param  array $relations           Relações a serem carregadas com o cache de endereço.
   * @param  bool $parse                Indica se o resultado deve ser parseado para DTO.
   * @return IntegrationAddressCacheDTO Objeto de transferência de dados do cache de endereço.
   */
  public function getById(int $id, array $relations = [], bool $parse = true) :IntegrationAddressCacheDTO {
    return $this->obIntegrationAddressCacheModel->getById($id, $relations, $parse);
  }

  /**
   * Obtém um cache de endereço por CEP.
   * @param  string $zipCode CEP do endereço a ser obtido.
   * @return IntegrationAddressCacheDTO Objeto de transferência de dados do cache de endereço.
   */
  public function getByZipCode(string $zipCode) :IntegrationAddressCacheDTO {
    $zipCode         = Functions::getNumbersOnly($zipCode);
    $addressDatabase = $this->obIntegrationAddressCacheModel->getByQuery([new Filter('zipcode', '=', $zipCode)], [], true);
    $expired         = $this->isCacheExpired($addressDatabase);

    if (!$addressDatabase instanceof IntegrationAddressCacheDTO || $expired) {
      $addressResolved = $this->externalAddressProvider->resolve($zipCode);
      
      $this->validateCache($addressResolved, $zipCode);

      $expired
        ? $addressResolved = $this->obIntegrationAddressCacheModel->edit($addressDatabase->id, (array)$addressResolved)
        : $addressResolved = $this->obIntegrationAddressCacheModel->create((array)$addressResolved);

      return $addressResolved;
    }
    
    return $addressDatabase;
  }

  /**
   * Valida um cache de endereço.
   * @param  IntegrationAddressCacheDTO|null $addressCache Cache de endereço a ser validado.
   * @param  string                          $zipCode      CEP do endereço para validação.
   * @return bool                                          Verdadeiro se o cache for válido, falso caso contrário.
   * @throws BusinessException                             Exceção lançada se o cache for inválido.
   */
  private function validateCache(?IntegrationAddressCacheDTO $addressCache, string $zipCode) :bool {
    if(!$addressCache instanceof IntegrationAddressCacheDTO)
      throw new BusinessException("Não foi possível obter o endereço para o CEP: $zipCode, verifique se o CEP está correto.", 404);

    return true;
  }

  /**
   * Verifica se um cache de endereço está expirado.
   * @param  IntegrationAddressCacheDTO|null $addressCache Cache de endereço a ser verificado.
   * @return bool                            Verdadeiro se o cache estiver expirado, falso caso contrário.
   */
  private function isCacheExpired(?IntegrationAddressCacheDTO $addressCache) :bool {
    if(!$addressCache instanceof IntegrationAddressCacheDTO)
      return false;

    $cacheTime   = strtotime($addressCache->expiresAt);
    $currentTime = time();

    // Considera o cache expirado se tiver mais de 24 horas (86400 segundos)
    return $currentTime > $cacheTime;
  }

}