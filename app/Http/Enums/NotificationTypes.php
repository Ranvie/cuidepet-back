<?php

namespace App\Http\Enums;

/**
 * Enumeração dos tipos de notificação disponíveis.
 */
enum NotificationTypes :string {
  case WELCOME                       = "welcome";
  case ANNOUNCEMENT_ALERT            = "announcement-alert";
  case NEW_RESPONSE                  = "new-response";
  case ANNOUNCEMENT_UPDATE           = "announcement-update";
  case PET_FOUND                     = "pet-found";
  case PET_ADOPTED                   = "pet-adopted";
  case ANNOUNCEMENT_PAUSED           = "announcement-paused";
  case FAVORITED_ANNOUNCEMENT_PAUSED = "favorited-announcement-paused";
  case FORM_PAUSED                   = "form-paused";
}