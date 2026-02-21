<?php

namespace System\Helper;

class Url
{

    public static function currentWithoutQueryParams(): string
    {
        $path = explode('?', $_SERVER['REQUEST_URI'])[0];

        return self::protocol() . '://' . $_SERVER['HTTP_HOST'] . $path;
    }

    public static function queryParams(): ?string
    {
        $exploded = explode('?', $_SERVER['REQUEST_URI']);

        return $exploded[1] ?? null;
    }

    public static function fullUrl(): string
    {
        $url = self::currentWithoutQueryParams();
        $query = self::queryParams();

        return $query ? $url . '?' . $query : $url;
    }

    public static function protocol(): string
    {
        $protocol = 'http';

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $protocol = 'https';
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            $protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'];
        }

        return $protocol;
    }

    public static function host(): string
    {
        return $_SERVER['HTTP_HOST'];
    }

    public static function domain(): string
    {
        return  self::protocol() . '://' . $_SERVER['HTTP_HOST'];
    }


    public static function port(): int
    {
        return (int) ($_SERVER['SERVER_PORT'] ?? 80);
    }


}