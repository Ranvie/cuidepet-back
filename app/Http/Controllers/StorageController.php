<?php

namespace App\Http\Controllers;

use App\Exceptions\BusinessException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Storage;

/**
 * Controlador responsável por lidar com requisições relacionadas ao armazenamento de arquivos.
 * Fornece métodos para acessar arquivos armazenados, como imagens ou outros recursos.
 */
class StorageController {
  
  /**
   * Método responsável por retornar um arquivo armazenado.
   * @param  string $path       Caminho do arquivo.
   * @return BinaryFileResponse Resposta contendo o arquivo solicitado ou um erro 404 se o arquivo não for encontrado.
   * @throws BusinessException  Se o arquivo solicitado não for encontrado.
   */
  public function get(string $path) :BinaryFileResponse {
    $fullPath = Storage::disk('public')->path($path);
    
    if(!file_exists($fullPath))
      throw new BusinessException('Arquivo não encontrado', 404);
    
    $mimeType = mime_content_type($fullPath);
    return response()->file($fullPath, ['Content-Type' => $mimeType]);
  }

}