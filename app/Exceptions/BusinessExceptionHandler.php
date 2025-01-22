<?php

namespace App\Exceptions;

use App\Http\Response\BusinessResponse;
use stdClass;

class BusinessExceptionHandler
{

    private int $status;

    public function __construct(
        private \Exception $exception
    ){
        $this->status = $this->mapErrorCode($this->exception);
    }

    public function render(){
        $responseData = new stdClass();
        $responseData->message = $this->exception->getMessage();
        if(method_exists($this->exception::class, 'errors')) $responseData->errors = $this->exception->errors();
;
        $response = new BusinessResponse($this->status, $responseData);

        return $response->build();
    }

    private function mapErrorCode($exception){

        $statusCode = match (true) {
            $exception instanceof \Illuminate\Validation\ValidationException => 422,
            $exception instanceof \Illuminate\Auth\AuthenticationException => 401,
            $exception instanceof \Illuminate\Auth\Access\AuthorizationException => 403,
            $exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException => 404,
            default => 500,
        };

        return $statusCode;
    }

}
