<?php

namespace App\Services;

use App\Classes\Filter;
use App\DTO\User\SafeUserDTO;
use App\DTO\User\UserDTO;
use App\DTO\UseTerms\UseTermsDTO;
use App\Exceptions\BusinessException;
use App\MessageDispatcher\Builders\EmailBuilder;
use App\MessageDispatcher\Orchestrator\MessageDispatcher;
use App\Models\NewsletterModel;
use App\Models\UserModel;
use App\Utils\PARSE_MODE;
use App\Utils\ParseConvention;
use App\Utils\Url;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use stdClass;

class AuthService {
  
  /**
   * Construtor do serviço de autenticação.
   * @param UserService     $userService     Serviço de usuários.
   * @param UseTermsService $useTermService  Serviço de termos de uso.
   * @param ParseConvention $parseConvention Utilitário para conversão de convenções de nomenclatura.
   * @param NewsletterModel $newsletterModel Modelo de newsletter para verificar assinaturas relacionadas ao usuário.
   */
  public function __construct(
    private UserService     $userService,
    private UseTermsService $useTermService,
    private ParseConvention $parseConvention,
    private NewsletterModel $newsletterModel
  ) {}

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

    $response                     = new stdClass();
    $expirationTime               = \floatval(config('token.login_expire_minutes'));
    $expiresAt                    = now()->addMinutes($expirationTime);
    $response->token              = $user->createToken($user->username . '-AuthToken', $abilities, $expiresAt->toDateTime())->plainTextToken;
    $response->useTermsAcceptance = $this->validateUseTermsAcceptance($user->id);
    
    $response->user                  = ParseConvention::parse($user->getOriginal(), PARSE_MODE::snakeToCamel, SafeUserDTO::class);
    $response->user->imageProfileUrl = $user->image_profile ? (new Url())->setResource('media')->getMediaUrlPath($user->image_profile) : null;
    $response->user->tokenExpiresAt  = $expiresAt->toDateTimeString();

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

    if(is_null($user->email_verified_at)){
      $this->sendEmailConfirmation($user);
      throw new BusinessException('Confirme seu email para acessar sua conta. Enviamos um email de confirmação para você.', 403);
    }

    return $user;
  }

  /**
   * Realiza o registro de um novo usuário.
   * @param  array $data Dados de registro, incluindo nome, email e senha.
   * @return UserDTO     Dados do usuário registrado.
   */
  public function register(array $data) :UserDTO {
    try {
      DB::beginTransaction();

      $obUserModel = $this->userService->create($data, parse: false);

      $this->acceptTerms($obUserModel->id);
      $this->verifyNewsletter($obUserModel);
      $this->sendEmailConfirmation($obUserModel);
      
      DB::commit();
    } catch (BusinessException $e) {
      DB::rollBack();
      throw $e;
    }

    return ParseConvention::parse($obUserModel->getOriginal(), PARSE_MODE::snakeToCamel, UserDTO::class);
  }

  /**
   * Envia um email de confirmação para o usuário registrado.
   * @param  UserModel $obUserModel Modelo do usuário para o qual o email de confirmação deve ser enviado.
   * @return void
   */  
  private function sendEmailConfirmation(UserModel $obUserModel) :void {
    $this->deleteTokens($obUserModel);

    $time      = \floatval(env('TOKEN_CONFIRM_EMAIL_EXPIRE_MINUTES'));
    $expiresAt = now()->addMinutes($time);
    $token     = $obUserModel->createToken("$obUserModel->username-ConfirmEmailToken", ['confirm-email'], $expiresAt->toDateTime())->plainTextToken;
    
    $frontUrl        = rtrim(config('app.front_url', env('APP_URL_FRONT', config('app.url'))), '/');
    $confirmationUrl = $frontUrl . "/confirm-email?token=" . urlencode($token);

    new MessageDispatcher(new EmailBuilder(
      [$obUserModel->email], 
      'CuidePet - Confirmação de e-mail', 
      'mail.emailConfirmation', 
      ['username' => $obUserModel->username, 'confirmationUrl' => $confirmationUrl]
    ))->dispatch();
  }
   
  /**
   * Confirma o email de um usuário usando um token de confirmação.
   * @return bool Indica se a confirmação foi bem-sucedida.
   */
  public function confirmUserEmail() :bool {
    $obUserModel = Auth::user();

    if ($obUserModel->email_confirmed)
      throw new BusinessException('Email já confirmado', 400);

    $this->userService->edit($obUserModel->id, ['email_verified_at' => now(), 'active' => 1]);

    $this->deleteTokens($obUserModel);

    return true;
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

    $time      = \floatval(env('TOKEN_RESETPASSWORD_EXPIRE_MINUTES'));
    $expiresAt = now()->addMinutes($time);
    $token     = $user->createToken("$userDto->username-ResetToken", ['reset-password'], $expiresAt->toDateTime())->plainTextToken;

    $frontUrl         = rtrim(config('app.front_url', env('APP_URL_FRONT', config('app.url'))), '/');
    $resetPasswordUrl = "$frontUrl/reset-password?token=" . urlencode($token);

    new MessageDispatcher(new EmailBuilder(
      [$userDto->email],
      'CuidePet - Recuperação de Senha', 
      'mail.recoverPassword', 
      ['username' => $userDto->username, 'resetUrl' => $resetPasswordUrl]
    ))->dispatch();
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
    DB::table('personal_access_tokens')
      ->where('tokenable_id', $userId)
      ->whereJsonContains('abilities', ['reset-password'])
      ->delete();
  }

  /**
   * Finaliza o processo de redefinição de senha
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
   * @return UseTermsDTO
   */
  public function getUseTerms() :UseTermsDTO {
    return $this->useTermService->getNewestUseTerms();
  }

  /**
   * Exibe os termos de uso para o usuário.
   * @return bool Indica se o usuário aceitou os termos de uso mais recentes.
   */
  public function validateUseTermsAcceptance(int $userId) :bool {
    return $this->useTermService->validateUseTermsAcceptance($userId);
  }

  /**
   * Aceita os termos de uso pelo usuário.
   * @param  int $userId       ID do usuário que está aceitando os termos de uso.
   * @return bool
   * @throws BusinessException Se ocorrer um erro ao aceitar os termos de uso.
   */
  public function acceptTerms(int $userId) :bool {
    return $this->useTermService->acceptTerms($userId);
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

  /**
   * Vincula uma newsletter ao usuário caso ele possua.
   * @param  UserModel $user Modelo do usuário para o qual a verificação deve ser realizada.
   * @return void
   */
  private function verifyNewsletter(UserModel $user) :void {
    $obNewsletter = $this->newsletterModel->getByQuery([new Filter('email', '=', $user->email)], [], false);

    if($obNewsletter instanceof NewsletterModel)
      $obNewsletter->edit($obNewsletter->id, ['user_id' => $user->id]);
  }
}
