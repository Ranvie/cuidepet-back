<?php

namespace App\Http\Enums;

/**
 * Enumeração responsável por definir os tamanhos máximos dos campos dos formulários
 */
class FormFieldsLength {
  public const MAX_PAGE         = 1;
  public const MAX_TITLE        = 255;
  public const MAX_INPUT        = 10;
  public const MAX_OPTIONS      = 10;
  public const MAX_OPTION_VALUE = 255;
  public const MAX_PLACEHOLDER  = 255;
  public const MAX_CHECKBOX     = 10;
  public const MAX_RADIO        = 1;
  public const MAX_TEXT         = 255;
  public const MAX_TEXTAREA     = 500;
}
