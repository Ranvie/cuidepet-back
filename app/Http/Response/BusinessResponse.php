<?php

namespace App\Http\Response;

use Illuminate\Http\JsonResponse;

class BusinessResponse
{
    public int $code = 200;
    public array|object|string $content;

    /**
     * @param int $code
     * @param array|object|string $content
     */
    public function __construct(int $code, object|array|string $content)
    {
        $this->code = $code;
        $this->content = $content;
    }

    public function build(): JsonResponse{
        return response()->json($this, $this->code);
    }

}
