<?php

namespace System\Trait;

use System\Helper\Helper;

trait RedirectTrait
{

    public function redirect(string $url, int $statusCode = 302): void
    {
        http_response_code($statusCode);

        header("Location: $url");
    }


    public function view(string $path, array $data = []): void
    {
        Helper::view($path, $data);
    }
}