<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthException extends BusinessException{

    public function __construct($message = "", int $code = 500){
        parent::__construct($message, $code);
    }

    public function render(Request $request): JsonResponse{
        return parent::render($request);
    }
}
