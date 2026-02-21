<?php

namespace System\Helper;

use ReflectionClass;
use ReflectionException;
use RuntimeException;
use System\Exceptions\ViewNotFoundException;
use System\Exceptions\ConfigFileNotExistsException;

class Helper
{

    /**
     * @throws ReflectionException
     */
    public static function resolve(string $class): object
    {
        $reflection = new ReflectionClass($class);


        $constructor = $reflection->getConstructor();

        if (!$constructor || !$constructor->getParameters()) {
            return $reflection->newInstance();
        }

        $constructorParams = [];
        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();

            if ($type && !$type->isBuiltin() && class_exists($type->getName())) {
                $constructorParams[] = static::resolve($type);
            } elseif ($param->isDefaultValueAvailable()) {
                $constructorParams[] = $param->getDefaultValue();
            } else {
                throw new RuntimeException("Cannot resolve parameter '{$param->getName()}' in class '{$class}'");
            }
        }

        return $reflection->newInstanceArgs($constructorParams);
    }

    public static function basePath(string $path): string
    {
        return $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . trim($path, '/');
    }

    /**
     * @throws ConfigFileNotExistsException
     */
    public static function getConfig(string $path, mixed $default = null): mixed
    {
        $key = explode('.', $path);

        if (count($key) <= 1) {
            return $default;
        }

        $fileName = $key[0];

        $file = static::basePath("config" . DIRECTORY_SEPARATOR . "$fileName.php");

        if (!file_exists($file)) {
            throw new ConfigFileNotExistsException("Config file '{$file}' does not exist");
        }

        $configFile = require $file;

        return static::array_get($configFile, trim(strstr($path, '.'), '.'));
    }


    public static function array_get(array $array, string $path, $default = null): mixed
    {
        $keys = explode('.', $path);

        foreach ($keys as $key) {
            if (is_array($array) && array_key_exists($key, $array)) {
                $array = $array[$key];
            } else {
                return $default;
            }
        }

        return $array;
    }


    public static function view(string $path, array $data = []): void
    {
        if ($data) {
            extract($data);
        }
        $path = rtrim(self::getConfig('view.base_path'), DIRECTORY_SEPARATOR) .
            DIRECTORY_SEPARATOR .
            str_replace('.', DIRECTORY_SEPARATOR, $path) . '.php';
        if (!file_exists($path)) {
            throw new ViewNotFoundException("View file '{$path}' does not exist");
        }
        require_once $path;
    }
}