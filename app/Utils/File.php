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
  protected string $resourcePath = __DIR__ . '/../../storage/app/public/';

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
    $path = $this->getResourcePath() . $path;

    if (file_exists($path))
      return unlink($path);

    return true;
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
