<?php

namespace App\Http\Controllers;

use App\Exceptions\BusinessException;
use App\Http\Requests\UserDetailRequest;
use App\Http\Requests\UserPasswordRequest;
use App\Http\Requests\UserRequest;
use App\Http\Response\BusinessResponse;
use Illuminate\Http\JsonResponse;
use App\Services\UserService;

class UserController {

  /**
   * Método Construtor
   * @param UserService $userService
   */
  public function __construct(
    private UserService $userService
  ) {}

  /**
   * Lista os usuários do banco de dados
   * @return JsonResponse
   */
  public function list() :JsonResponse {
    $users = $this->userService->getList(10, 1);

    $response = new BusinessResponse(200, $users);
    return $response->build();
  }

  /**
   * Obtém um usuário por ID
   * @param  int $userId
   * @return JsonResponse
   */
  public function get(int $userId) :JsonResponse {
    $user = $this->userService->getById($userId);

    $response = new BusinessResponse(200, $user);
    return $response->build();
  }

  /**
   * Obtém o perfil do usuário autenticado
   * @return JsonResponse
   */
  public function getProfile() :JsonResponse {
    $userId = auth()->id();
    $user   = $this->userService->getById($userId, ['preference', 'newsletter.addresses']);

    $response = new BusinessResponse(200, $user);
    return $response->build();
  }

  /**
   * Atualiza o perfil do usuário autenticado
   * @param  UserDetailRequest $request
   * @return JsonResponse
   */
  public function updateProfile(UserDetailRequest $request) :JsonResponse {
    $userId      = auth()->id();
    $requestData = $request->validated();

    $user = $this->userService->edit($userId, $requestData);
    //TODO: Falta a questão do address

    $response = new BusinessResponse(200, $user);
    return $response->build();
  }

  /**
   * Atualiza a senha do usuário autenticado
   * @param  UserPasswordRequest $request
   * @return JsonResponse
   * @throws BusinessException
   */
  public function updatePassword(UserPasswordRequest $request) :JsonResponse {
    $user        = auth()->user();
    $requestData = $request->validated();

    if(!password_verify($requestData['currentPassword'], $user->password))
      throw new BusinessException("A senha atual está incorreta.", 400);

    $requestData['password'] = $requestData['newPassword'];
    unset($requestData['currentPassword'], $requestData['newPassword'], $requestData['passwordConfirmation']);
    $this->userService->edit($user->id, $requestData);

    $response = new BusinessResponse(200, "Senha atualizada com sucesso.");
    return $response->build();
  }

  /**
   * Cria um novo usuário
   * @param  UserRequest $request
   * @return JsonResponse
   */
  public function create(UserRequest $request) :JsonResponse {
    $requestData = $request->validated();
    $user        = $this->userService->create($requestData);

    $response = new BusinessResponse(201, $user);
    return $response->build();
  }

  /**
   * Atualiza um usuário existente
   * @param  int         $userId
   * @param  UserRequest $request
   * @return JsonResponse
   */
  public function update(int $userId, UserRequest $request) :JsonResponse {
    $requestData = $request->validated();
    $user = $this->userService->edit($userId, $requestData);

    $response = new BusinessResponse(200, $user);
    return $response->build();
  }

  /**
   * Deleta um usuário
   * @param  int $userId
   * @return JsonResponse
   */
  public function delete(int $userId) :JsonResponse {
    $this->userService->remove($userId);

    $response = new BusinessResponse(200, "O usuário {$userId} foi deletado com sucesso.");
    return $response->build();
  }

  /**
   * Inativa o usuário autenticado
   * @return JsonResponse
   */
  public function inactivate() :JsonResponse {
    $userId = auth()->id();
    $this->userService->inactivate($userId);

    $response = new BusinessResponse(200, "O usuário {$userId} foi inativado com sucesso.");
    return $response->build();
  }
}
