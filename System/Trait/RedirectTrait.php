<?php

namespace System\Trait;

trait RedirectTrait
{

    public function redirect(string $url, int $statusCode = 302): void
    {
        http_response_code($statusCode);

        header("Location: $url");
    }
}