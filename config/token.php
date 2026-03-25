<?php

return [
    'login_expire_minutes' => env('TOKEN_LOGIN_EXPIRE_MINUTES', 10),
    'confirm_email_expire_minutes' => env('TOKEN_CONFIRM_EMAIL_EXPIRE_MINUTES', 30),
    'resetpassword_expire_minutes' => env('TOKEN_RESETPASSWORD_EXPIRE_MINUTES', 30),
];