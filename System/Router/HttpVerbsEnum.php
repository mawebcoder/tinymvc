<?php

namespace System\Router;

enum HttpVerbsEnum: string
{
    case GET = 'GET';
    case HEAD = 'HEAD';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case OPTIONS = 'OPTIONS';
    case PATCH = 'PATCH';

    public function hasBody(): bool
    {
        return match ($this) {
            self::POST,
            self::PUT,
            self::DELETE,
            self::OPTIONS,
            self::PATCH => true,
            default => false,
        };
    }
}
