<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Response\BusinessResponse;
use Illuminate\Http\JsonResponse;
use App\Services\UserService;

class UserController extends Controller {

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
   * Inativa um usuário
   * @param  int $userId
   * @return JsonResponse
   */
  public function inactivate(int $userId) :JsonResponse {
    $this->userService->inactivate($userId);

    $response = new BusinessResponse(200, "O usuário {$userId} foi inativado com sucesso.");
    return $response->build();
  }
}
