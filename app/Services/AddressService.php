<?php

namespace App\Services;

use App\DTO\Address\AddressDTO;
use App\DTO\IntegrationAddressCache\IntegrationAddressCacheDTO;
use App\Exceptions\BusinessException;
use App\Models\AddressModel;
use App\Services\AddressCacheService;
use App\Services\Interfaces\IService;

class AddressService implements IService {

  /**
   * Construtor do serviço de endereços.
   * @param AddressModel $obAddressModel Modelo de endereço.
   */
  public function __construct(
    private AddressModel $obAddressModel,
    private AddressCacheService $addressCacheService
  ) {}

  /**
   * Lista os endereços com paginação.
   * @param  int $limit Número de endereços por página.
   * @param  int $page  Número da página.
   * @return array      Lista de endereços paginada.
   */
  public function getList(int $limit, int $page) :array {
    return $this->obAddressModel->list($limit, $page);
  }

  /**
   * Obtém um endereço por ID.
   * @param  int   $id               ID do endereço.
   * @param  array $relations        Relações a serem carregadas com o endereço.
   * @param  bool  $parse            Indica se o resultado deve ser parseado para DTO.
   * @return AddressModel|AddressDTO Objeto de transferência de dados do endereço.
   */
  public function getById(int $id, array $relations = [], bool $parse = true) :AddressDTO {
    $obAddressModelDTO = $this->obAddressModel->getById($id, $relations, $parse);

    return $obAddressModelDTO;
  }

  /**
   * Cria um novo endereço.
   * @param  array $data       Dados do endereço a ser criado.
   * @return AddressDTO        Objeto de transferência de dados do endereço criado.
   * @throws BusinessException Exceção lançada se o endereço não puder ser criado.
   */
  public function create(array $data) :AddressDTO {
    $addressCache = $this->addressCacheService->getByZipCode($data['zipCode']);

    $data['integrationAddressCacheId'] = $addressCache->id;
    return $this->obAddressModel->create($data, ['cacheAddress'], true);
  }

  /**
   * Edita um endereço existente.
   * @param  int   $id   ID do endereço a ser editado.
   * @param  array $data Dados do endereço a ser editado.
   * @return AddressDTO  Objeto de transferência de dados do endereço editado.
   */
  public function edit(int $id, array $data) :AddressDTO {
    $addressCache = $this->addressCacheService->getByZipCode($data['zipCode'] ?? '');

    if(!$addressCache instanceof IntegrationAddressCacheDTO)
      throw new BusinessException('Endereço inválido. Não foi possível encontrar o endereço para o CEP informado.');

    $data['integrationAddressCacheId'] = $addressCache->id;
    return $this->obAddressModel->edit($id, $data);
  }

  /**
   * Remove um endereço.
   * @param  int|null $id ID do endereço a ser removido.
   * @return bool         Indica se a remoção foi bem-sucedida.
   */
  public function remove(?int $id = null): bool {
    return $this->obAddressModel->remove($id);
  }

}