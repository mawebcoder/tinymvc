<?php

namespace System\Helper;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Exception;
use RuntimeException;

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
}