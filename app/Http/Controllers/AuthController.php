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

class AuthController {
  
  /**
   * Método Construtor
   * @param AuthService $authService
   */
  public function __construct(
    private AuthService $authService
  ) {}

  /**
   * Realiza o login de um usuário
   * @param  LoginRequest $request Requisição contendo as credenciais de login do usuário
   * @return JsonResponse          Resposta JSON contendo o token de acesso do usuário
   * @throws BusinessException     Se o usuário for inválido: credenciais incorretas, usuário não encontrado, conta desativada, etc.
   */
  public function login(LoginRequest $request) :JsonResponse {
    $loginData = $request->validated();
    $token     = $this->authService->login($loginData);

    $response = new BusinessResponse(200, $token);
    return $response->build();
  }

  /**
   * Realiza o registro de um novo usuário
   * @param  RegisterRequest $request Requisição contendo os dados de registro do usuário
   * @return JsonResponse             Resposta JSON contendo os detalhes do usuário registrado
   */
  public function register(RegisterRequest $request) :JsonResponse {
    $registerData = $request->validated();
    $user         = $this->authService->register($registerData);

    $response = new BusinessResponse(200, $user);
    return $response->build();
  }

  /**
   * Inicia o processo de recuperação de senha para um usuário
   * @param  RecoveryRequest $request Requisição contendo o email do usuário para recuperação de senha
   * @return JsonResponse             Resposta JSON indicando que um email de recuperação de senha foi enviado, caso o email exista no sistema
   */
  public function recoveryPassword(RecoveryRequest $request) :JsonResponse {
    $recoveryData = $request->validated();
    $this->authService->recoveryPassword($recoveryData);

    $response = new BusinessResponse(200, "Um email de alteração de senha foi enviado, verifique sua caixa de entrada.");
    return $response->build();
  }

  /**
   * Realiza a alteração da senha de um usuário
   * @param  ResetPasswordRequest $pwdRequest Requisição contendo os dados necessários para alteração de senha (token de recuperação, nova senha, etc.)
   * @return JsonResponse                     Resposta JSON indicando que a senha foi alterada com sucesso
   */
  public function resetPassword(ResetPasswordRequest $pwdRequest) :JsonResponse {
    $pwdData = $pwdRequest->validated();

    $this->authService->resetPassword($pwdData);
    $response = new BusinessResponse(200, "A senha de sua conta foi alterada com sucesso");
    return $response->build();
  }

  /**
   * Realiza a confirmação de e-mail do usuário
   * @return JsonResponse Resposta JSON indicando que o e-mail foi confirmado
   */
  public function confirmUserEmail() :JsonResponse {
    $this->authService->confirmUserEmail();
    $response = new BusinessResponse(200, "E-mail confirmado com sucesso");
    return $response->build();
  }

  /**
   * Retorna os termos de uso para o usuário
   * @return void
   */
  public function useTerms() :void {
    $this->authService->useTerms();
  }

  /**
   * Registra a aceitação dos termos de uso por parte do usuário
   * @return void
   */
  public function acceptTerm(int $useTermId, int $userId) :void {
    $this->authService->acceptTerm($useTermId, $userId);
  }

  /**
   * Realiza o logout do usuário autenticado, invalidando seus tokens de acesso
   * @return JsonResponse Resposta JSON indicando que o logout foi efetuado com sucesso
   */
  public function logout() :JsonResponse {
    $this->authService->logout(auth()->id());

    $response = new BusinessResponse(200, "Logout efetuado com sucesso");
    return $response->build();
  }
  
}
