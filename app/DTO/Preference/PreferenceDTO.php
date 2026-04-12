<?php

namespace App\DTO\Preference;

class PreferenceDTO {

  /**
   * Identificador da preferência
   * @var int
   */
  public int $userId;

  /**
   * Indica se o usuário deseja receber alertas regionais
   * @var bool
   */
  public bool $receiveRegionAlarms;

  /**
   * Indica se o usuário deseja receber alertas por email
   * @var bool
   */
  public bool $receiveAlarmsOnEmail;
}
