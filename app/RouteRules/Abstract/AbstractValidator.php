<?php

namespace App\RouteRules\Abstract;

use App\Exceptions\BusinessException;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractValidator {

  /**
   * Armazena dados úteis como ação atual, dados da URL, usuário autenticado, etc, para serem utilizados na validação de regras de pertencimento.
   * @var array
   */
  protected array $quickContext = [];

  /**
   * Método Construtor
   * @param Request $request Objeto Request contendo os dados da requisição HTTP.
   * @param mixed   $params  Parâmetros adicionais para a validação de pertencimento.
   */
  public function __construct(
    protected Request $request,
    protected mixed   $params
  ) {
    $this->initializeContext();
  }

  /**
   * Inicializa o contexto rápido com dados úteis para a validação de regras de pertencimento.
   * Este método é chamado antes da execução da validação para garantir que o contexto esteja disponível para as classes filhas.
   * @return void
   */
  protected function initializeContext() :void {
    $this->quickContext['action']     = $this->request->route()->getActionMethod() ?? '';
    $this->quickContext['parameters'] = $this->request->route()->parameters()      ?? [];
    $this->quickContext['user']       = auth()->user()                             ?? null;
    $this->quickContext['params']     = $this->params                              ?? [];
  }

  /**
   * Método abstrato para validar regras de pertencimento.
   * Este método deve ser implementado por classes filhas para definir as regras específicas de validação.
   * @return void
   * @throws BusinessException lançada quando a validação de pertencimento falha.
   */
  abstract public function validate(): void;
  
}