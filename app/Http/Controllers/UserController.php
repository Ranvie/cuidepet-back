<?php

namespace App\Http\Controllers;

use App\Exceptions\BusinessException;
use App\Http\Requests\ListingRequest;
use App\Http\Requests\UserDetailRequest;
use App\Http\Requests\UserPasswordRequest;
use App\Http\Requests\UserRequest;
use App\Http\Response\BusinessResponse;
use App\Services\NewsletterService;
use Illuminate\Http\JsonResponse;
use App\Services\UserService;

/**
 * Controlador responsável por gerenciar as operações relacionadas aos usuários.
 * Fornece métodos para listar, obter, criar, atualizar e deletar usuários, bem como
 * atualizar o perfil e a senha do usuário autenticado.
 */
class UserController {

  /**
   * Método Construtor
   * @param UserService       $userService,
   * @param NewsletterService $newsletterService
   */
  public function __construct(
    private UserService       $userService,
    private NewsletterService $newsletterService
  ) {}

  /**
   * Lista os usuários do banco de dados
   * @param  ListingRequest $request Requisição contendo os parâmetros de paginação
   * @return JsonResponse
   */
  public function list(ListingRequest $request) :JsonResponse {
    $validated = $request->validated();
    $users     = $this->userService->getList($validated['limit'], $validated['page']);

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
    $user        = auth()->user();
    $requestData = $request->validated();

    $groupedAddresses = $this->groupAddressesByAction($requestData['addresses'] ?? []);
    $groupedCeps      = [
      'ADD' => array_map(fn($address) => $this->prepareZipcode($address['cep']), $groupedAddresses['ADD']),
      'DEL' => array_map(fn($address) => $this->prepareZipcode($address['cep']), $groupedAddresses['DEL'])
    ];

    $this->userService->edit($user->id, $requestData);
    $this->newsletterService->subscribe($groupedCeps['ADD'], $user->email, $user->id);
    $this->newsletterService->unsubscribe($groupedCeps['DEL'], $user->email);

    $user     = $this->userService->getById($user->id, ['preference', 'roles', 'forms', 'newsletter.addresses']);
    $response = new BusinessResponse(200, $user);
    return $response->build();
  }

  /**
   * Agrupa os endereços por ação (ADD ou DEL).
   * @param  array $addresses Lista de endereços com ações.
   * @return array            Endereços agrupados por ação.
   */
  private function groupAddressesByAction(array $addresses) :array {
    $grouped = ['ADD' => [], 'DEL' => []];

    foreach ($addresses as $address) {
      if (isset($address['action']) && \in_array($address['action'], ['ADD', 'DEL'])) {
        $action = $address['action'];
        unset($address['action']);
        $grouped[$action][] = $address;
      }
    }

    return $grouped;
  }

  /**
   * Prepara um CEP, removendo caracteres não numéricos.
   * @param  string $zipcode CEP a ser preparado.
   * @return string          CEP preparado, contendo apenas números.
   */
  private function prepareZipcode(string $zipcode) :string {
    return preg_replace('/\D/', '', $zipcode);
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
