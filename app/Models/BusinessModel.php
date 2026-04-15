<?php

namespace App\Models;

use App\Classes\Filter;
use App\Classes\Ordenation;
use App\Utils\PARSE_MODE;
use Illuminate\Database\Eloquent\Model;
use App\Utils\ParseConvention;
use App\Utils\Objectfy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Modelo de negócio base para os modelos do sistema.
 * Fornece métodos genéricos para operações comuns, como listagem, criação, edição e remoção de registros.
 */
class BusinessModel extends Model {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected $class = '';

  /**
   * Define campos created_at e updated_at gerenciados pelo láravel
   * @var bool
   */
  public $timestamps = false;

  /**
   * Objeto de conversão de classes
   * @var ParseConvention
   */
  private ParseConvention $obParseConvention;

  /**
   * Define o número máximo de itens por página para listagem.
   * @var int
   */
  public const int MAX_ITEMS_PER_PAGE = 100;

  /**
   * Método Construtor
   */
  public function __construct() {
    $this->obParseConvention = new ParseConvention;
  }

  /**
   * Converte os objetos do banco de dados para o formato definido na propriedade $class
   * @param  Model|Collection $registers
   * @param  string|null      $class
   * @return object
   */
  public function parser(Model|Collection $registers, ?string $class = null) :object {
    $parsed = !$registers instanceof Collection
      ? $this->obParseConvention::parse($registers->attributesToArray(), PARSE_MODE::snakeToCamel, $class ?? $registers->class)
      : $registers->map(function ($obModel) {
        return $this->obParseConvention::parse($obModel->attributesToArray(), PARSE_MODE::snakeToCamel, $obModel->class);
      });

    //TODO: Está inserindo as relações mesmo quando não está no DTO;
    if (isset($registers->relations))
      foreach ($registers->relations as $key => $register) $register === null ?: $parsed->$key = $this->parser($register);

    return $parsed;
  }

  /**
   * Lista os registros do banco de dados
   * @param  int          $limit
   * @param  int          $page
   * @param  int|null     $hardCodedMaxItems
   * @param  string[]     $relations
   * @param  Filter[]     $filters
   * @param  Ordenation[] $orders
   * @return array
   */
  public function list(int $limit = 10, int $page = 1, ?int $hardCodedMaxItems = null, array $relations = [], array $filters = [], array $orders = []) :array {
    $hardCodedMaxItems = $hardCodedMaxItems ?: self::MAX_ITEMS_PER_PAGE;

    if ($limit > $hardCodedMaxItems) 
      $limit = $hardCodedMaxItems;

    $query = self::query();
    $this->addFilters($query, $filters);
    $this->ordenation($query, $orders);

    $query->with($relations);
    $registers = $query->paginate($limit, ['*'], 'page', $page);

    $parsedRegisters = [];
    foreach ($registers as $register) {
      $parsedRegisters[] = $this->parser($register);
    }

    return $this->getListResponse(
      $parsedRegisters,
      $registers->perPage(),
      $registers->currentPage(),
      $registers->lastPage(),
      $registers->total(),
      $hardCodedMaxItems
    );
  }

  /**
   * Formata a resposta da listagem de registros
   * @param  array    $parsedRegisters   Registros já convertidos para o formato de saída
   * @param  int      $perPage           Número de itens por página
   * @param  int      $currentPage       Página atual
   * @param  int      $lastPage          Última página disponível
   * @param  int      $total             Total de registros encontrados
   * @param  int|null $hardCodedMaxItems Limite máximo de itens por página (para controle interno, não necessariamente igual ao $perPage)
   * @return array                       Resposta formatada para a API, contendo os registros e informações de paginação
   */
  public static function getListResponse(array $parsedRegisters, int $perPage, int $currentPage, int $lastPage, int $total, ?int $hardCodedMaxItems) :array {
    $parsed                = [];
    $parsed['registers']   = $parsedRegisters;
    $parsed['perPage']     = $perPage;
    $parsed['lastPage']    = $lastPage;
    $parsed['currentPage'] = $currentPage;
    $parsed['total']       = $total;
    $parsed['maxItems']    = $hardCodedMaxItems;

    return $parsed;
  }

  /**
   * Procura um registro por ID
   * @param  int      $id
   * @param  string[] $relations
   * @param  boolean  $parse
   * @return null|object
   */
  public function getById(int $id, array $relations = [], bool $parse = true) :?object {
    $query = self::query();
    $this->addFilters($query);

    $model = $query->where($this->primaryKey, $id)->with($relations)->first();
    if (!$model instanceof $this) return null;
    if (!$parse) return $model;

    return $this->parser($model);
  }

  /**
   * Procura um registro por uma query personalizada
   * @param  string[] $relations
   * @param  boolean  $parse
   * @return null|object
   */
  public function getByQuery(array $filters, array $relations = [], bool $parse = true) :?object {
    $queryBuilder = self::query();
    $this->addFilters($queryBuilder, $filters);

    $model = $queryBuilder->with($relations)->first();
    if (!$model instanceof $this) return null;
    if (!$parse) return $model;

    return $this->parser($model);
  }

  /**
   * Adiciona filtros a uma query
   * @param  Builder $query
   * @param  Filter[] $filters
   * @return void
   */
  private function addFilters(Builder $query, array $filters = []) :void {
    foreach ($filters as $filter) {
      if (str_contains($filter->column, '.')) {
        $this->addRelationFilter($query, $filter);
      } else {
        $query->where($filter->column, $filter->operator, $filter->value, $filter->boolean);
      }
    }
  }

  /**
   * Adiciona filtro em relacionamento
   * @param  Builder $query
   * @param  Filter  $filter
   * @return void
   */
  private function addRelationFilter(Builder $query, Filter $filter) :void {
    $parts    = explode('.', $filter->column);
    $column   = array_pop($parts);
    $relation = implode('.', $parts);

    if (!method_exists($this, $relation))
      return;
    
    $method = strtoupper($filter->boolean) === 'OR' 
      ? 'orWhereHas'
      : 'whereHas';
    
    $query->$method($relation, function($q) use ($column, $filter) {
      $q->where($column, $filter->operator, $filter->value);
    });
  }

  /**
   * Adiciona ordenação a uma query
   * @param  Builder      $query
   * @param  Ordenation[] $orders
   * @return void
   */
  private function ordenation(Builder $query, array $orders = []) :void {
    foreach ($orders as $order) {
      $column = $order->field     ?? null;
      $dir    = $order->direction ?? 'asc';
      
      if (!$column) 
        continue;
      
      if (str_contains($column, '.')) {
        $this->addRelationOrdenation($query, $column, $dir);
      } else {
        $query->orderBy($column, $dir);
      }
    }
  }

  /**
   * Adiciona ordenação em relacionamento
   * @param  Builder $query
   * @param  string  $column
   * @param  string  $dir
   * @return void
   */
  private function addRelationOrdenation(Builder $query, string $column, string $dir) :void {
    $parts          = explode('.', $column);
    $relationColumn = array_pop($parts);
    $relationName   = implode('.', $parts);

    if (!method_exists($this, $relationName))
      return;
    
    $relation = $this->$relationName();
    
    $query->orderBy(
      $relation->getRelated()
        ->select($relationColumn)
        ->whereColumn(
          $relation->getQualifiedForeignKeyName(),
          $relation->getQualifiedOwnerKeyName()
        )
        ->limit(1),
      $dir
    );
  }


  /**
   * @param  array    $data
   * @param  string[] $relations
   * @param  boolean  $parse
   * @return object
   */
  public function create(array $data, array $relations = [], bool $parse = true) :object {
    if (empty($data)) {
      $this->save();
      return $this->getById($this->original[$this->primaryKey], $relations, $parse);
    }

    return
      DB::transaction(function () use ($data, $relations, $parse) {
        $origin = ParseConvention::parse($data, PARSE_MODE::camelToSnake);

        $this->fill($origin);
        $this->save();

        return $this->getById($this->original[$this->primaryKey], $relations, $parse);
      });
  }

  /**
   * Atualiza o registro no banco de dados
   * @param  int          $id
   * @param  array|object $data
   * @param  boolean      $ignoreNulls
   * @param  boolean      $parse
   * @return null|object
   */
  public function edit(int $id, array|object $data, bool $ignoreNulls = true, bool $parse = true) :?object {
    $register = $this->getById($id, parse: false);
    if (!$register instanceof $this) return null;

    $origin = ParseConvention::parse($data, PARSE_MODE::camelToSnake);
    $destin = $register->original;
    $obj    = Objectfy::transferTo($origin, $destin, $ignoreNulls);

    $register->fill($obj);
    $register->save();

    return $this->getById($id, [], $parse);
  }

  /**
   * Apaga um registro no banco de dados
   * @param  int|null $id
   * @return bool
   */
  public function remove(?int $id = null) :bool {
    $id = $id ?? $this->original['id'];
    
    if (!$id) 
      return false;

    $query = self::query();
    $query->where('id', $id);

    return $query->delete();
  }

  /**
   * Cria uma nova instância do modelo
   * @return static
   */
  public function newModel() :static {
    return new static();
  }
  
}
