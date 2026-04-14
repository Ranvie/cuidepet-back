<?php

namespace App\Services;

use App\Classes\Filter;
use App\Exceptions\BusinessException;
use App\Http\Response\BusinessResponse;
use App\Models\NewsletterModel;

/**
 * Serviço responsável por gerenciar as operações relacionadas à newsletter, incluindo
 * assinatura, cancelamento de assinatura, envio de newsletters e obtenção de assinantes.
 */
class NewsletterService {

  /**
   * Método Construtor
   * @param NewsletterModel     $newsletterModel,
   * @param AddressCacheService $addressCacheService
   */
  public function __construct(
    private NewsletterModel     $newsletterModel,
    private AddressCacheService $addressCacheService
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
      $obNewsletter->addresses()->attach($cache->id);
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
      ->pluck('tb_integration_address_cache.id')
      ->toArray();

    $obNewsletter->addresses()->detach($addressCacheIds);
  }

  public function sendNewsletter() :void {
    // Lógica para enviar a newsletter para os assinantes
  }

  public function getSubscribers(string $regionZipcode) :array {
    // Lógica para obter a lista de assinantes da newsletter com base no CEP da região
    return [];
  }
}