<?php

namespace App\Exceptions;

use App\Http\Response\BusinessResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function __construct($message = "", int $code = 500)
    {
        parent::__construct($message, $code);
    }

    public function render(Request $request): JsonResponse
    {
        $response = new BusinessResponse($this->code, $this->message);

        return response()->json($response, $this->code);
    }
}
