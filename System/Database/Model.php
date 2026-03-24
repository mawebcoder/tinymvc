<?php

namespace System\Database;


use ReflectionClass;

use System\Database\Driver\PDODriver;

class Model
{

    public static PDODriver $connection;
    protected string $table;

    public function __construct()
    {
        $initializer = static function () {
            return new PDODriver();
        };

        $reflection = new ReflectionClass(PDODriver::class);

        self::$connection = $reflection->newLazyProxy($initializer);
    }

    public function table(): string
    {
        $table = '';

        $classNameExploded = explode('\\', static::class);

        $name = array_pop($classNameExploded);

        foreach (str_split($name) as $index => $char) {
            if (preg_match('/[A-Z]/', $char)) {
                if ($index === 0) {
                    $table .= strtolower($char);
                    continue;
                }
                $table .= '_' . strtolower($char);
                continue;
            }
            $table .= $char;
        }

        $this->table = $table;

        return $table;
    }


}