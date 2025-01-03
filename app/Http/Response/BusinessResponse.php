<?php

namespace App\Http\Response;

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

}
