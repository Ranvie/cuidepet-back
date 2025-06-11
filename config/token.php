<?php

return [
    'login_expire_minutes' => env('TOKEN_LOGIN_EXPIRE_MINUTES', 10),
    'resetpassword_expire_minutes' => env('TOKEN_RESETPASSWORD_EXPIRE_MINUTES', 30),
];