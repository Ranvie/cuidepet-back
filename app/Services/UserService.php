<?php

namespace App\Services;

use App\Classes\Filter;
use App\DTO\User\UserDTO;
use App\Exceptions\BusinessException;
use App\Http\Enums\DefaultUserForm;
use App\Http\Enums\NotificationTypes;
use App\MessageDispatcher\Builders\NotificationBuilder;
use App\MessageDispatcher\Orchestrator\MessageDispatcher;
use App\Models\UserModel;
use App\Services\Interfaces\IUserService;
use App\Utils\File;
use Illuminate\Support\Facades\DB;

class UserService implements IUserService {

  /**
   * Método Construtor
   * @param UserModel   $userModel
   * @param FormService $formService
   */
  public function __construct(
    private UserModel           $userModel,
    private FormService         $formService,
    private AddressCacheService $obAddressService,
  ) {}

  /**
   * Lista os usuários do banco de dados
   * @param  int   $limit Número de registros por página
   * @param  int   $page  Número da página
   * @param  array $relations Relacionamentos a serem carregados
   * @param  array $filters Filtros a serem aplicados
   * @param  array $orders Ordenações a serem aplicadas
   * @return array
   */
  public function getList(int $limit, int $page, array $relations = ['preference', 'roles', 'forms'], array $filters = [], array $orders = []) :array {
    return $this->userModel->list($limit, $page, relations: $relations, filters: $filters, orders: $orders);
  }

  /**
   * Obtém um usuário por ID
   * @param  int   $id        ID do usuário a ser buscado
   * @param  array $relations Relacionamentos a serem carregados
   * @param  bool  $parse     Indica se deve converter o resultado para UserDTO
   * @return UserDTO|UserModel
   */
  public function getById(int $id, array $relations = ['preference', 'roles', 'forms'], $parse = true) :UserDTO|UserModel {
    $user = $this->userModel->getById($id, $relations, $parse);

    $this->validateIfUserExists($user);
    return $user;
  }

  /**
   * Obtém um usuário por email
   * @param  string $email        Email do usuário a ser buscado
   * @param  bool   $parse        Indica se deve converter o resultado para UserDTO
   * @param  bool   $simulateHash Indica se deve simular a verificação de hash para evitar diferenças de tempo na resposta
   * @return UserDTO|UserModel
   */
  public function getByEmail(string $email, bool $parse = true, bool $simulateHash = false) :UserDTO|UserModel {
    $user = $this->userModel->getByEmail($email, $parse);

    $this->validateIfUserExists($user, $simulateHash);
    return $user;
  }

  /**
   * Cria um novo usuário
   * @param  array $data      Dados do usuário a ser criado
   * @param  array $relations Relacionamentos a serem carregados no retorno
   * @param  bool  $parse     Indica se deve converter o resultado para UserDTO
   * @return UserDTO|UserModel
   */
  public function create(array $data, array $relations = ['preference', 'roles', 'forms'], bool $parse = true) :UserDTO|UserModel {
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

    try {
      DB::beginTransaction();

      $user = $this->userModel->create($data, [], false);
      $user->preference()->create();
      $user->roles()->sync([2]);

      (new MessageDispatcher(new NotificationBuilder([$user->id], NotificationTypes::WELCOME)))->dispatch();

      $userId = $user->getOriginal()['id'];
      $this->formService->create([
        'userId'  => $userId,
        'title'   => DefaultUserForm::TITLE->value,
        'payload' => file_get_contents(resource_path('documents/cuidepet-default-user-form.json'))
      ]);
      
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      throw new BusinessException('Ocorreu um erro ao criar o usuário. Tente novamente mais tarde. Detalhes: ' . $e->getMessage(), 500);
    }

    return $this->userModel->getById($userId, $relations, $parse);
  }

  /**
   * Edita um usuário existente
   * @param  int   $id   ID do usuário a ser editado
   * @param  array $data Dados atualizados do usuário
   * @return UserDTO|UserModel
   */
  public function edit(int $id, array $data) :UserDTO|UserModel {
    $userDto = $this->userModel->getById($id, ['preference'], true);
    $this->validateIfUserExists($userDto);

    try {
      DB::beginTransaction();

      if (isset($data['password']))
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

      if(isset($data['imageProfile'])) {
        $obFile = (new File("user/{$userDto->id}/profile/"));
        $obFile->remove($userDto->imageProfile);
        
        $data['imageProfile'] = $obFile->save($data['imageProfile'], width: 1200, height: 700);
      }

      $user = $this->userModel->edit($id, $data ?? [], parse: false);
      $user->preference()->getModel()->edit($userDto->preference->userId, $data['preference'] ?? []);

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      throw new BusinessException('Ocorreu um erro ao editar o usuário. Tente novamente mais tarde. Detalhes: ' . $e->getMessage(), 500);
    }
    
    return $this->userModel->getById($id, ['preference', 'roles', 'forms', 'newsletter.addresses'], true);
  }

  /**
   * Remove um usuário
   * @param  int|null $id ID do usuário a ser removido, ou null para remover o usuário atual
   * @return bool
   */
  public function remove(?int $id = null) :bool {
    return $this->userModel->remove($id);
  }

  /**
   * Inativa um usuário
   * @param  string   $password Senha do usuário para confirmar a inativação
   * @param  int|null $id       ID do usuário a ser inativado
   * @return bool
   */
  public function inactivate(string $password, ?int $id = null) :bool {
    $user = $this->userModel->getById($id, ['newsletter'], false);
    $this->validateIfUserExists($user);

    if (!password_verify($password, $user->password))
      throw new BusinessException("A senha atual está incorreta.", 400);

    try {
      DB::beginTransaction();

      // Inativar usuário, formulários e anúncios
      $this->userModel->edit($user->id, ['active' => false], parse: false);
      $user->forms()->update(['active' => false]);
      $user->announcements()->update(['active' => false]);

      // Revogar tokens de acesso (segurança)
      $user->tokens()->delete();

      // Remover permissões
      $user->roles()->detach();

      // Remover dados operacionais pessoais
      $user->notifications()->delete();
      $user->preference()->delete();
      $user->favorites()->detach();

      // Remover newsletter e endereços associados
      if ($user->newsletter) {
        $user->newsletter->addresses()->detach();
        $user->newsletter()->delete();
      }

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      throw new BusinessException('Ocorreu um erro ao inativar o usuário. Tente novamente mais tarde. Detalhes: ' . $e->getMessage(), 500);
    }

    return true;
  }

  /**
   * Obtém os usuários que estão em uma determinada área geográfica com base em códigos postais e raio
   * @param  string $regionZipcode Lista de códigos postais que definem o centro da área geográfica
   * @param  float  $radius        Raio em quilômetros para definir a área geográfica ao redor dos códigos postais
   * @return array                 Lista de usuários que estão dentro da área geográfica definida
   */
  public function getUsersInArea(string $regionZipcode, float $radius = 5) :array {
    $addresses  = $this->obAddressService->getAddressesInArea($regionZipcode, $radius);
    $addressIds = array_map(fn($a) => $a->id, $addresses);

    return $this->userModel->getAllByQuery([
      new Filter('addresses.address_cache_id', 'IN', $addressIds)
    ]);
  }

  /**
   * Valida se o usuário existe, lançando uma exceção caso contrário
   * @param  UserDTO|UserModel|null $user         Usuário a ser verificado
   * @param  bool                   $simulateHash Indica se deve simular a verificação de hash para evitar diferenças de tempo na resposta
   * @throws BusinessException                    Se o usuário não for encontrado
   */
  private function validateIfUserExists(UserDTO|UserModel|null $user, bool $simulateHash = false) {
    if (!$user instanceof UserDTO && !$user instanceof UserModel) {
      if($simulateHash) password_hash("simulateHash", PASSWORD_DEFAULT);
      throw new BusinessException('O usuário não foi encontrado.', 404);
    }
  }
}
