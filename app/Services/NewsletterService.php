<?php

namespace App\Services;

use App\Classes\Filter;
use App\Exceptions\BusinessException;
use App\Http\Response\BusinessResponse;
use App\MessageDispatcher\Builders\EmailBuilder;
use App\MessageDispatcher\Orchestrator\MessageDispatcher;
use App\Models\NewsletterModel;
use App\Models\NewsletterIntegrationAddressCacheModel;
use App\Utils\Functions;

/**
 * Serviço responsável por gerenciar as operações relacionadas à newsletter, incluindo
 * assinatura, cancelamento de assinatura, envio de newsletters e obtenção de assinantes.
 */
class NewsletterService {

  /**
   * Método Construtor
   * @param NewsletterModel                        $newsletterModel,
   * @param AddressCacheService                    $addressCacheService,
   * @param NewsletterIntegrationAddressCacheModel $newsletterIntegrationAddressCacheModel,
   * @param TokenService                           $tokenService
   */
  public function __construct(
    private NewsletterModel                        $newsletterModel,
    private AddressCacheService                    $addressCacheService,
    private NewsletterIntegrationAddressCacheModel $newsletterIntegrationAddressCacheModel,
    private TokenService                           $tokenService
  ) {}

  /**
   * Assina um usuário na newsletter com base em seus e-mails e ID de usuário.
   * @param  array  $zipCodes Lista de códigos postais para segmentação da newsletter
   * @param  string $email    E-mail do usuário para assinatura da newsletter
   * @param  int    $userId   Id do usuário dono da newsletter
   * @return void
   */
  public function subscribe(array $zipCodes, string $email, ?int $userId = null) :void {
    $zipCodes = array_map(fn($zip) => preg_replace('/\D/', '', $zip), $zipCodes);

    $obNewsletter = $this->newsletterModel->getByQuery([new Filter('email', '=', $email)], ['addresses'], false);
    if(!$obNewsletter instanceof NewsletterModel){
      $data['userId'] = $userId;
      $data['email']  = $email;
      $obNewsletter   = $this->newsletterModel->create($data, [], false);
    }

    $this->validateAlreadyRegisteredNewsletters($obNewsletter, $zipCodes);

    $registeredNewsletters = $obNewsletter->addresses()->count();
    $newNewsletters        = \count($zipCodes);

    if($registeredNewsletters + $newNewsletters > 5)
      throw new BusinessException('Só podem haver 5 newsletters ativas por conta.', 400);
    
    $caches = $this->getAddressCaches($zipCodes);
    foreach($caches ?? [] as $cache){
      $obNewsletter->addresses()->attach($cache->id, ['hash' => $this->getNewsletterHash()]);
    }
  }

  /**
   * Verifica e remove os códigos postais enviados na request que já estão registrados na newsletter.
   * @param  NewsletterModel $newsletter Newsletter para a qual os códigos postais estão sendo validados
   * @param  array           &$zipCodes  Lista de códigos postais a serem validados e atualizados
   * @return void
   */
  private function validateAlreadyRegisteredNewsletters(NewsletterModel $newsletter, array &$zipCodes) :void {
    $registeredZipCodes = $newsletter->addresses()->pluck('zipcode')->toArray();
    foreach($zipCodes ?? [] as $zipCode){
      if(\in_array($zipCode, $registeredZipCodes))
        unset($zipCodes[array_search($zipCode, $zipCodes)]);
    }
  }

  /**
   * Cria um cache de endereço para a newsletter com base no modelo de newsletter e no código postal.
   * @param  array $zipCodes                     Lista de códigos postais para os quais o cache de endereço será criado
   * @return array ['zipCode' => 'addressCache'] Retorna um array associativo onde a chave é o código postal e o valor é o ID do cache de endereço criado
   */
  private function getAddressCaches(array $zipCodes) :array {
    $caches = [];
    foreach($zipCodes ?? [] as $zipCode){
      try {
        if(isset($caches[$zipCode]))
          continue;

        $obAddressCache = $this->addressCacheService->getByZipCode($zipCode);
      } catch (BusinessException $e) {
        BusinessResponse::addErrors("O CEP $zipCode é inválido.");
        continue;
      }

      $caches[$zipCode] = $obAddressCache;
    }

    return $caches;
  }

  /**
   * Cancela a assinatura de um usuário na newsletter com base em seus e-mails e códigos postais.
   * @param  array  $zipCodes Lista de códigos postais para os quais a assinatura da newsletter será cancelada
   * @param  string $email    E-mail do usuário para cancelamento da assinatura da newsletter
   * @return void
   * @throws BusinessException Se a newsletter não for encontrada
   */
  public function unsubscribe(array $zipCodes, string $email) :void {
    $zipCodes     = array_map(fn($zip) => preg_replace('/\D/', '', $zip), $zipCodes);
    $obNewsletter = $this->newsletterModel->getByQuery([new Filter('email', '=', $email)], ['addresses'], false);
    if(!$obNewsletter instanceof NewsletterModel)
      throw new BusinessException('Newsletter não encontrada para este e-mail.', 404);

    $foundZipCodes    = $obNewsletter->addresses->pluck('zipcode')->toArray();
    $notFoundZipCodes = array_diff($zipCodes, $foundZipCodes);
    if(\count($notFoundZipCodes) > 0)
      BusinessResponse::addErrors("Os seguintes CEPs não estão registrados na newsletter: " . implode(', ', $notFoundZipCodes));

    $addressCacheIds = $obNewsletter->addresses
      ->whereIn('zipcode', $zipCodes)
      ->pluck('id')
      ->toArray();

    $obNewsletter->addresses()->detach($addressCacheIds);
  }

  /**
   * Obtém os assinantes da newsletter com base em um código postal e um raio de distância.
   * OBS: As preferências não são levadas em conta.
   * @param  string $regionZipcode Código postal da região para a qual os assinantes serão obtidos
   * @param  int    $radius        Raio de distância em quilômetros para a obtenção dos assinantes
   * @return array                 Lista de assinantes da newsletter na região especificada
   */
  public function getSubscribers(string $regionZipcode, int $radius = 5) :array {
    $addressesInArea = $this->addressCacheService->getAddressesInArea($regionZipcode, $radius);
    $addressIds      = array_map(fn($address) => $address->id, $addressesInArea);

    $filters   = [];
    $filters[] = new Filter('address_cache_id', 'IN', $addressIds);

    $newsletters = $this->newsletterIntegrationAddressCacheModel->getAllByQuery($filters, ['newsletter.user.preference'], true);
    return $newsletters;
  }

  /**
   * Envia um e-mail de confirmação de assinatura da newsletter para o usuário.
   * @param  string $email   E-mail do usuário para o qual o e-mail de confirmação será enviado
   * @param  string $zipCode Código postal associado à assinatura da newsletter para o qual o e-mail de confirmação será enviado
   * @return void
   */
  public function sendNewsletterMailConfirmation(string $email, string $zipCode) :void {
    $token           = $this->tokenService->createToken('newsletter-subscription', ['email' => $email, 'zipCode' => $zipCode], 60);
    $subscriptionUrl = url(config('app.frontend_url') . "/newsletter/confirm?token=$token");

    new MessageDispatcher(
      new EmailBuilder([$email],'Confirmação de Assinatura da Newsletter','mail.newsletterConfirmation',['subscriptionUrl' => $subscriptionUrl])
    )->dispatch();
  }

  /**
   * Confirma a assinatura de um usuário na newsletter com base em um token de confirmação.
   * @param  string $token Token de confirmação da assinatura da newsletter
   * @return void
   * @throws BusinessException Se o token for inválido ou expirado, ou se ocorrer algum erro durante a confirmação da assinatura
   */
  public function confirmNewsletterSubscription(string $token) :void {
    $obToken = $this->tokenService->verifyToken('newsletter-subscription', $token);

    $payload = json_decode($obToken->payload, true);
    if(!$payload || !isset($payload['email']) || !isset($payload['zipCode']))
      throw new BusinessException('Token de confirmação inválido.', 400);

    $this->subscribe([$payload['zipCode']], $payload['email']);
  }

  /**
   * Cancela a assinatura de um usuário na newsletter com base em um token de cancelamento.
   * @param  string $token Token de cancelamento da assinatura da newsletter
   * @return void
   */
  public function unsubscribeByToken(string $token) :void {
    $obNewsletterIntegrationModel = $this->newsletterIntegrationAddressCacheModel->getByQuery([new Filter('hash', '=', $token)], [], false);
    $obNewsletterIntegrationModel->delete();
  }

  /**
   * Gera um hash único para a newsletter.
   * @return string Hash único gerado para a newsletter
   */
  private function getNewsletterHash() :string {
    do {
      $hash = Functions::getRandomHash(32);
    } while($this->newsletterIntegrationAddressCacheModel->getByQuery([new Filter('hash', '=', $hash)], [], false) instanceof NewsletterIntegrationAddressCacheModel);
    return $hash;
  }
}