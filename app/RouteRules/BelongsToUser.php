<?php

namespace App\RouteRules;

use App\Exceptions\BusinessException;
use App\RouteRules\Abstract\AbstractValidator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Classe abstrata para validação de regras de pertencimento.
 * Esta classe serve como base para a implementação de regras específicas de validação de pertencimento, como verificar se um recurso pertence a um usuário autenticado.
 */
class BelongsToUser extends AbstractValidator {

  /**
   * Valida se o recurso pertence ao usuário autenticado.
   * Este método verifica se o recurso identificado pelos parâmetros da rota pertence ao usuário autenticado, com base na chave estrangeira fornecida.
   * @return void
   * @throws BusinessException lançada quando a validação de pertencimento falha.
   */
  public function validate(): void {
    $user       = $this->quickContext['user'];
    $foreignKey = 'user_id';

    if(!$user) {
      throw new BusinessException('Usuário não autenticado.', 401);
    }

    $model = $this->quickContext['params'][1] ?? null;
    
    try{
      $obModel   = app('App\\Models\\' . $model);
      $idParam   = $this->quickContext['parameters']['id']      ?? null;
      $idParam ??= array_pop($this->quickContext['parameters']) ?? null;

      $obModel->where($foreignKey, $user->id)
              ->where($obModel->getKeyName(), $idParam ?? 0)
              ->firstOrFail();
    }
    catch(ModelNotFoundException $e){
      throw new BusinessException('Recurso não encontrado.', 404);
    }catch(\Exception $e){
      throw new BusinessException("O modelo $model não existe.", 500);
    }

  }
  
}