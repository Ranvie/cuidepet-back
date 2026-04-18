<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\DTO\IntegrationAddressCache\IntegrationAddressCacheDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Expression;
use App\Utils\StateConversor;

/**
 * Modelo de cache de endereço para integrações com APIs externas.
 * Esta classe representa a estrutura e as operações relacionadas ao cache de endereços obtidos de integrações externas, como a API do AwesomeCep.
 * Ela é responsável por armazenar informações como latitude, longitude, CEP, estado, cidade, bairro, rua, fonte dos dados e data de expiração do cache.
 * O modelo também define as relações com outras entidades do sistema, como newsletters e endereços de anúncios.
 */
class IntegrationAddressCacheModel extends BusinessModel {

  /**
   * Aponta a classe DTO associada a este modelo
   * @var string
   */
  protected $class = IntegrationAddressCacheDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public $table = 'tb_integration_address_cache';

  /**
   * Aponta a chave primária no banco de dados
   * @var string
   */
  public $primaryKey = 'id';

  /**
   * Define a chave primária como auto incremento
   * @var bool
   */
  public $incrementing = true;

  /**
   * Define campos created_at e updated_at gerenciados pelo láravel
   * @var bool
   */
  public $timestamps = true;

  /**
   * Desativa o campo created_at já que não é necessário para esta tabela
   */
  const CREATED_AT = null;

  /**
   * Define os campos que podem ser preenchidos em massa
   * @var array
   */
  public $fillable = [
    'point',
    'latitude',
    'longitude',
    'zipcode',
    'state',
    'city',
    'neighborhood',
    'street',
    'source',
    'expires_at'
  ];

  /**
   * @param  array    $data
   * @param  string[] $relations
   * @param  boolean  $parse
   * @return object
   */
  public function create(array $data, array $relations = [], bool $parse = true) :object {
    $data['point'] = $this->getPoint($data['latitude'], $data['longitude']);
    
    $uf            = StateConversor::isStateAbbreviation($data['state']) ? $data['state'] : StateConversor::getStateAbbreviation($data['state']);
    $data['state'] = $uf ?? $data['state'];
    
    return parent::create($data, $relations, $parse);
  }

  /**
   * Gera um ponto geográfico a partir de latitude e longitude para ser armazenado no banco de dados.
   * @param  string $latitude  Latitude do ponto geográfico.
   * @param  string $longitude Longitude do ponto geográfico.
   * @return Expression        Representação em texto do ponto geográfico para uso em consultas SQL.
   */
  private function getPoint(string $latitude, string $longitude) :Expression {
    $point = "POINT({$longitude} {$latitude})";
    return DB::raw("ST_GeomFromText('$point')");
  }

  /**
   * Edita um cache de endereço existente.
   * @param  int          $id          ID do cache de endereço a ser editado.
   * @param  array|object $data        Dados a serem atualizados no cache de endereço.
   * @param  bool         $ignoreNulls Indica se os valores nulos devem ser ignorados na atualização.
   * @param  bool         $parse       Indica se os dados devem ser parseados antes da atualização.
   * @return object                    Objeto do cache de endereço atualizado.
   */
  public function edit(int $id, array|object $data, bool $ignoreNulls = true, bool $parse = true) :object {
    if(isset($data['latitude']) && isset($data['longitude'])){
      $data['point'] = $this->getPoint($data['latitude'], $data['longitude']);
    }

    if(isset($data['state'])){
      $uf            = StateConversor::isStateAbbreviation($data['state']) ? $data['state'] : StateConversor::getStateAbbreviation($data['state']);
      $data['state'] = $uf ?? $data['state'];
    }
    
    return parent::edit($id, $data, $ignoreNulls, $parse);
  }

  /**
   * Obtém os usuários que estão na área de um código postal específico.
   * @param  string $latitude  Latitude do código postal para o qual os usuários próximos devem ser encontrados.
   * @param  string $longitude Longitude do código postal para o qual os usuários próximos devem ser encontrados.
   * @param  int    $radius    Raio em quilômetros para considerar os usuários próximos ao código postal.
   * @return array             Coleção de endereços encontrados na área especificada.
   */
  public function getAddressesInArea(string $latitude, string $longitude, int $radius = 5) {
    return DB::table('tb_integration_address_cache')
            ->selectRaw('*, ST_Distance_Sphere(point, POINT(?, ?)) AS distance_m', [$longitude, $latitude])
            ->whereRaw('ST_Distance_Sphere(point, POINT(?, ?)) <= ?', [$longitude, $latitude, $radius * 1000])
            ->get()
            ->toArray();
  }

  /**
   * Recupera as newsletters relacionadas a este cache de endereço. Um cache de endereço pode estar relacionado a muitas newsletters.
   * @return BelongsToMany
   */
  public function newsletters() :BelongsToMany {
    return $this->belongsToMany(
      NewsletterModel::class,
      NewsletterIntegrationAddressCacheModel::class,
      'integration_address_cache_id',
      'newsletter_id'
    );
  }

  /**
   * Recupera os endereços de anúncios relacionados a este cache de endereço. Um cache de endereço pode estar relacionado a muitos endereços de anúncios.
   * @return HasMany
   */
  public function announcementAddresses() :HasMany {
    return $this->hasMany(AddressModel::class, 'integration_address_cache_id', 'id');
  }

}