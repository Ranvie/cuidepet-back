<?php

namespace App\Services;

use App\DTO\User\SafeUserDTO;
use App\DTO\User\UserDTO;
use App\Events\RecoverPasswordEvent;
use App\Exceptions\BusinessException;
use App\Models\UserModel;
use App\Utils\PARSE_MODE;
use App\Utils\ParseConvention;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use stdClass;

class AuthService {
  
  /**
   * Construtor do serviço de autenticação.
   * @param UserService     $userService     Serviço de usuários.
   * @param ParseConvention $parseConvention Utilitário para conversão de convenções de nomenclatura.
   */
  public function __construct(
    private UserService $userService,
    private ParseConvention $parseConvention
  ) {}

  //TODO: Fazer uma espécie de refreshToken
  /**
   * Realiza o login de um usuário.
   * @param  array $data Dados de login, incluindo email e senha.
   * @return stdClass    Dados do usuário autenticado e token de acesso.
   */
  public function login(array $data) :stdClass {
    $user = $this->validateUser($data);
    $this->deleteTokens($user);
    $abilities = [];

    foreach ($user->roles as $role) {
      $abilities[] = $role->name;
    }

    $response        = new stdClass();
    $expirationTime  = \floatval(config('token.login_expire_minutes'));
    $expiresAt       = now()->addMinutes($expirationTime);
    $response->token = $user->createToken($user->username . '-AuthToken', $abilities, $expiresAt->toDateTime())->plainTextToken;
    $response->user  = ParseConvention::parse($user->getOriginal(), PARSE_MODE::snakeToCamel, SafeUserDTO::class);
    return $response;
  }

  /**
   * Valida as credenciais do usuário para login.
   * @param  array $data       Dados de login, incluindo email e senha.
   * @return UserModel|null    Modelo do usuário autenticado.
   * @throws BusinessException Se as credenciais forem inválidas.
   */
  private function validateUser(array $data) :UserModel|null {
    $user = null;

    try {
      $user = $this->userService->getByEmail($data['email'], false, true);

      if (!Hash::check($data['password'], $user->password))
        throw new BusinessException();

    } catch (BusinessException $e) {
      throw new BusinessException('Verifique suas credenciais e tente novamente', 400);
    }

    return $user;
  }

  /**
   * Realiza o registro de um novo usuário.
   * @param  array $data Dados de registro, incluindo nome, email e senha.
   * @return UserDTO     Dados do usuário registrado.
   */
  public function register(array $data) :UserDTO {
    return $this->userService->create($data);
  }

  /**
   * Inicia o processo de recuperação de senha para um usuário.
   * @param  array $data Dados de recuperação, incluindo email do usuário.
   * @return void
   */
  public function recoveryPassword(array $data) :void {
    $user = $this->validateEmailExists($data['email'] ?? null);
    if(!$user instanceof UserModel) 
      return;

    $userDto = $this->parseConvention->parse($user->getOriginal(), PARSE_MODE::snakeToCamel, UserDTO::class);
    $this->deleteResetPasswordTokens($userDto->id);

    $expiresAt = now()->addMinutes(env('TOKEN_RESETPASSWORD_EXPIRE_MINUTES'));
    $token     = $user->createToken("$userDto->username-ResetToken", ['reset-password'], $expiresAt->toDateTime())->plainTextToken;

    $frontUrl         = rtrim(config('app.front_url', env('APP_URL_FRONT', config('app.url'))), '/');
    $resetPasswordUrl = "$frontUrl/reset-password?token=" . urlencode($token);
    RecoverPasswordEvent::dispatch($userDto, $resetPasswordUrl);
  }

  /**
   * Valida se um email já está registrado no sistema.
   * @param  string $email  Email a ser verificado.
   * @return UserModel|null Modelo do usuário encontrado ou null se não existir.
   */
  private function validateEmailExists(string $email) :?UserModel {
    try {
      $user = $this->userService->getByEmail($email, false);
      return $user;
    } catch (BusinessException $e) {
      return null;
    }
  }

  /**
   * Exclui os tokens de recuperação de senha associados a um usuário.
   * @param  int  $userId ID do usuário para o qual os tokens devem ser excluídos.
   * @return void
   */
  private function deleteResetPasswordTokens(int $userId) :void {
    $tokens = DB::table('personal_access_tokens')
      ->where('tokenable_id', $userId)
      ->whereJsonContains('abilities', ['reset-password'])
      ->get();

    foreach ($tokens as $token) {
      DB::table('personal_access_tokens')->where('id', $token->id)->delete();
    }
  }

  /**
   * Redefine a senha de um usuário autenticado.
   * @param  array $pwdData    Dados de nova senha, incluindo a nova senha e confirmação.
   * @return bool              Indica se a redefinição foi bem-sucedida.
   * @throws BusinessException Se o usuário não estiver autenticado.
   */
  public function resetPassword($pwdData) :bool {
    $userId = Auth::id();

    if($userId === null)
      throw new BusinessException('Usuário não autenticado', 401);

    $this->userService->edit($userId, $pwdData);

    $this->deleteResetPasswordTokens($userId);
    return true;
  }

  /**
   * Exibe os termos de uso para o usuário.
   * @return void
   */
  public function useTerms() :void {
    dd("UseTherms");
  }

  /**
   * Aceita os termos de uso pelo usuário.
   * @return void
   */
  public function acceptTerms() :void {
    dd("AcceptTherms");
  }

  /**
   * Realiza o logout do usuário, invalidando seus tokens de acesso.
   * @param  int  $userId ID do usuário que está realizando o logout.
   * @return bool         Indica se o logout foi bem-sucedido.
   */
  public function logout(int $userId) :bool {
    $user = $this->userService->getById($userId, [], false);

    $this->deleteTokens($user);
    return true;
  }

  /**
   * Exclui todos os tokens de acesso associados a um usuário.
   * @param  UserModel $user Modelo do usuário para o qual os tokens devem ser excluídos.
   * @return void
   */
  private function deleteTokens(UserModel $user) :void {
    $user->tokens()->delete();
  }
}
