<?php

namespace System\Database;

use PDO;
use System\Helper\Helper;
use Exception;
use System\Exceptions\DatabaseConnectionException;
use System\Exceptions\ConfigFileNotExistsException;

class Model
{
    public static ?PDO $pdo=null;


    /**
     * @throws ConfigFileNotExistsException
     * @throws DatabaseConnectionException
     */
    public static function connect(): void
    {
        if (!is_null(self::$pdo)) {
            return;
        }

        [$host, $port, $database, $username, $password] = static::getConfigs();

        foreach (range(1, 5) as $try) {
            try {
                self::$pdo = new PDO(
                    "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4",
                    $username,
                    $password
                );

                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                break;
            } catch (Exception $exception) {
                sleep(1);

                if ($try === 5) {
                    throw new DatabaseConnectionException($exception->getMessage());
                }

                continue;
            }
        }
    }
    /**
     * @return array
     * @throws ConfigFileNotExistsException
     */
    private static function getConfigs(): array
    {
        $host = Helper::getConfig('database.host');
        $port = Helper::getConfig('database.port');
        $database = Helper::getConfig('database.database');
        $username = Helper::getConfig('database.username');
        $password = Helper::getConfig('database.password');
        return array($host, $port, $database, $username, $password);
    }
}