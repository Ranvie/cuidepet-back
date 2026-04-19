<?php

namespace App\Http\Enums;

/**
 * Enumeração responsável por definir os tipos de entrada dos formulários
 */
enum FormInputType: string {
  CASE CHECKBOX = 'checkbox';
  CASE RADIO    = 'radio';
  CASE TEXT     = 'text';
  CASE TEXTAREA = 'textarea';
  CASE NUMBER   = 'number';
  CASE DROPDOWN = 'dropdown';
  CASE DATE     = 'date';
}
