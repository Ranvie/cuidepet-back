<?php

namespace App\Services;

use App\DTO\User\UserDTO;
use App\Exceptions\BusinessException;
use App\Http\Enums\DefaultUserForm;
use App\Models\UserModel;
use App\Services\Interfaces\IUserService;

class UserService implements IUserService {

  /**
   * Método Construtor
   * @param UserModel   $userModel
   * @param FormService $formService
   */
  public function __construct(
    private UserModel $userModel,
    private FormService $formService
  ) {}

  /**
   * Lista os usuários do banco de dados
   * @param  int $limit Número de registros por página
   * @param  int $page  Número da página
   * @return array
   */
  public function getList(int $limit, int $page) :array {
    return $this->userModel->list($limit, $page);
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

    $user = $this->userModel->create($data, [], false);
    $user->preference()->create();
    $user->roles()->sync([2]);

    $userId = $user->getOriginal()['id'];
    $this->formService->create([
      'userId'  => $userId,
      'title'   => DefaultUserForm::TITLE,
      'payload' => DefaultUserForm::PAYLOAD
    ]);

    return $this->userModel->getById($userId, $relations, $parse);
  }

  /**
   * Edita um usuário existente
   * @param  int   $id   ID do usuário a ser editado
   * @param  array $data Dados atualizados do usuário
   * @return UserDTO|UserModel
   */
  public function edit(int $id, array $data) :UserDTO|UserModel {
    if (isset($data['password'])) $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    $user = $this->userModel->edit($id, $data);

    $this->validateIfUserExists($user);
    return $user;
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
   * @param  int|null $id ID do usuário a ser inativado, ou null para inativar o usuário atual
   * @return bool
   */
  public function inactivate(?int $id = null) :bool {
    return $this->userModel->inactivate($id);
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
