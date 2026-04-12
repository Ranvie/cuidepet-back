<?php

namespace App\Utils;

use App\Exceptions\BusinessException;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

/**
 * Classe utilitária para operações de arquivo.
 * Fornece métodos para salvar, ler e remover arquivos do sistema de arquivos.
 */
class File {

  /**
   * Caminho base para recursos, como arquivos de mídia.
   * @var string $resourcePath
   */
  protected string $resourcePath;

  /**
   * Subdiretório opcional dentro do diretório de recursos.
   * @var string $subdirectory
   */
  protected string $subdirectory = '';

  /**
   * Construtor da classe File.
   * Verifica se o diretório de recursos existe e o cria se necessário.
   * Permite a criação de subdiretórios dentro do diretório de recursos.
   * @param string $subdirectory Subdiretório opcional dentro do diretório de recursos.
   */
  public function __construct(string $subdirectory) {
    $this->resourcePath = storage_path('\\app\\public') . DIRECTORY_SEPARATOR;

    if(!file_exists($this->resourcePath)) {
      mkdir($this->resourcePath, 0755, true);
    }

    if($subdirectory) {
      $this->subdirectory = rtrim($subdirectory, '/') . '/';
      if (!file_exists($this->getFullPath())) {
        mkdir($this->getFullPath(), 0755, true);
      }
    }
  }

  /**
   * Método para salvar um arquivo enviado via multipart/form-data.
   * @param  UploadedFile $file         Arquivo enviado via multipart/form-data.
   * @param  array        $allowedMimes Lista de extensões de arquivo permitidas para validação.
   * @param  int          $height       Altura para redimensionamento da imagem (padrão: 100).
   * @param  int          $width        Largura para redimensionamento da imagem (padrão: 100).
   * @return string                     Caminho completo do arquivo salvo, incluindo o nome do arquivo.
   * @throws BusinessException          Se a extensão do arquivo não for permitida ou se a operação falhar.
   */
  public function save(UploadedFile $file, array $allowedMimes = ['png', 'jpg', 'jpeg', 'webp'], int $height = 100, int $width = 100) :string {
    $path      = $this->getFullPath() . $file->hashName();
    $extension = $file->getClientOriginalExtension();

    if(!\in_array($extension, $allowedMimes))
      throw new BusinessException("A Extensão de arquivo '{$extension}' não é permitida.");

    $manager = new ImageManager(new Driver());
    $image   = $manager->decodePath($file->getPathname());
    $image->scale((int)$width, (int)$height);
    $image->save($path);

    return $this->getRelativePath($path);
  }

  /**
   * Método para remover um arquivo do sistema de arquivos.
   * @param  string|null $path Caminho do arquivo a ser removido.
   * @return bool              Indica se a remoção foi bem-sucedida ou se o arquivo não existia.
   */
  public function remove(string|null $path) :bool {
    if(trim($path) === '')
      return true;
    
    $this->securityMeasures($path);
    $path = $this->getResourcePath() . $path;

    if (file_exists($path))
      return unlink($path);

    return true;
  }

  /**
   * Método para remover todos os arquivos e subdiretórios dentro do diretório de recursos.
   * @return bool Indica se a remoção foi bem-sucedida ou se o diretório não existia.
   */
  public function removeAll() :bool {
    return $this->removeDirectory(rtrim($this->getFullPath(), '/'));
  }

  /**
   * Método para remover um diretório e todo o seu conteúdo recursivamente.
   * @param  string|null $subdirectory Subdiretório a ser removido dentro do diretório de recursos. Se null, o diretório de recursos será removido.
   * @return bool                      Indica se a remoção foi bem-sucedida ou se o diretório não existia.
   */
  private function removeDirectory(string $subdirectory = '') :bool {
    $this->securityMeasures($subdirectory);

    if (!is_dir($subdirectory))
      return false;
    
    $items = array_diff(scandir($subdirectory), ['.', '..']);
    foreach ($items as $item) {
      $itemPath = $subdirectory . DIRECTORY_SEPARATOR . $item;
      
      if (is_dir($itemPath)) {
        $this->removeDirectory($itemPath);
      } else {
        unlink($itemPath);
      }
    }
    
    return rmdir($subdirectory);
  }

  /**
   * Método para aplicar medidas de segurança ao manipular subdiretórios.
   * Verifica se o subdiretório contém tentativas de acesso a diretórios superiores ou fora do recurso.
   * @param  string $subdirectory Subdiretório a ser verificado.
   * @throws BusinessException    Se o subdiretório for considerado inseguro.
   */
  private function securityMeasures(string $subdirectory) :void {
    if (trim($subdirectory) === '')
      return;

    if (strpos($subdirectory, "\0") !== false)
      throw new BusinessException('Caminho inválido: caracteres nulos não são permitidos.', 400);

    if (strpos($subdirectory, '..') !== false)
      throw new BusinessException('Caminho inválido: o acesso a diretórios superiores não é permitido.', 400);
    
    $this->validateIfPathIsWithinResource($subdirectory);
  }

  /**
   * Método para validar se o caminho fornecido está dentro do diretório de recursos.
   * Previne tentativas de acesso a diretórios fora do recurso, mesmo que o path traversal seja evitado.
   * @param  string $path      Caminho a ser validado.
   * @throws BusinessException Se o caminho estiver fora do diretório de recursos.
   */
  private function validateIfPathIsWithinResource(string $path) :void {
    $subdirectory = str_replace('\\', '/', $path);
    
    $resourcePath     = $this->getResourcePath();
    $realFullPath     = realpath($subdirectory);
    $realResourcePath = realpath($resourcePath);
    
    if ($realFullPath !== false) {
      $realFullPath     = str_replace('\\', '/', $realFullPath);
      $realResourcePath = str_replace('\\', '/', $realResourcePath);
      
      if (strpos($realFullPath, $realResourcePath) !== 0)
        throw new BusinessException('Caminho inválido: o acesso a diretórios fora do recurso "{caminho_projeto}/storage/app/public" não é permitido.', 400);
    }
  }

  /**
   * Método para obter o caminho base dos recursos.
   * @return string Caminho base dos recursos.
   */
  public function getResourcePath() :string {
    return $this->resourcePath;
  }

  /**
   * Método para obter o subdiretório dentro do diretório de recursos.
   * @return string Subdiretório dentro do diretório de recursos.
   */
  public function getSubdirectory() :string {
    return $this->subdirectory;
  }

  /**
   * Método para obter o caminho completo do diretório de recursos, incluindo o subdiretório.
   * @return string Caminho completo do diretório de recursos, incluindo o subdiretório.
   */
  public function getFullPath() :string {
    return $this->resourcePath . $this->subdirectory;
  }

  /**
   * Método para obter o caminho relativo de um arquivo com base no caminho absoluto.
   * @param  string $absolutePath Caminho absoluto do arquivo.
   * @return string               Caminho relativo do arquivo em relação ao diretório de recursos.
   */
  public function getRelativePath(string $absolutePath) :string {
    return str_replace($this->resourcePath, '', $absolutePath);
  }

}
