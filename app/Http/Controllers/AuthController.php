<?php

namespace App\Http\Controllers;

use App\Exceptions\BusinessException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RecoveryRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Response\BusinessResponse;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller {
  
  /**
   * Método Construtor
   * @param AuthService $authService
   */
  public function __construct(
    private AuthService $authService
  ) {}

  //TODO: muito importante, tratar o caso em que o usuário não existe no banco, uma vez que dá para notar a diferença de tempo na resposta
  //quando o email existe ou não no banco (por conta da conversão da hash)
  /**
   * Realiza o login de um usuário
   * @param  LoginRequest $request
   * @return JsonResponse
   * @throws BusinessException Se o usuário for inválido: credenciais incorretas, usuário não encontrado, conta desativada, etc.
   */
  public function login(LoginRequest $request) :JsonResponse {
    $loginData = $request->validated();
    $token     = $this->authService->login($loginData);

    $response = new BusinessResponse(200, $token);
    return $response->build();
  }

  /**
   * Realiza o registro de um novo usuário
   * @param  RegisterRequest $request
   * @return JsonResponse
   */
  public function register(RegisterRequest $request) :JsonResponse {
    $registerData = $request->validated();
    $user         = $this->authService->register($registerData);

    $response = new BusinessResponse(200, $user);
    return $response->build();
  }

  /**
   * Inicia o processo de recuperação de senha para um usuário
   * @param  RecoveryRequest $request
   * @return JsonResponse
   */
  public function recoveryPassword(RecoveryRequest $request) :JsonResponse {
    $recoveryData = $request->validated();
    $this->authService->recoveryPassword($recoveryData);

    $response = new BusinessResponse(200, "Um email de alteração de senha foi enviado, verifique sua caixa de entrada.");
    return $response->build();
  }

  /**
   * Realiza a alteração da senha de um usuário
   * @param  ResetPasswordRequest $pwdRequest
   * @return JsonResponse
   */
  public function resetPassword(ResetPasswordRequest $pwdRequest) :JsonResponse {
    $pwdData = $pwdRequest->validated();

    $this->authService->resetPassword($pwdData);
    $response = new BusinessResponse(200, "A senha de sua conta foi alterada com sucesso");
    return $response->build();
  }

  /**
   * Verifica se o usuário já aceitou os termos de uso
   * @return void
   */
  public function useTerms() :void {
    $this->authService->useTerms();
  }

  /**
   * Registra a aceitação dos termos de uso por parte do usuário
   * @return void
   */
  public function acceptTerms() :void {
    $this->authService->acceptTerms();
  }

  /**
   * Realiza o logout do usuário, invalidando seus tokens de acesso
   * @param  int $userId ID do usuário a ser deslogado
   * @return JsonResponse
   */
  public function logout(int $userId) :JsonResponse {
    $this->authService->logout($userId);

    $response = new BusinessResponse(200, "Logout efetuado com sucesso");
    return $response->build();
  }
  
}
