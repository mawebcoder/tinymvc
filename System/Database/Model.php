<?php

namespace System\Database;

use PDO;
use System\Helper\Helper;
use Exception;
use System\Exceptions\DatabaseConnectionException;
use System\Exceptions\ConfigFileNotExistsException;

class Model
{
    public static PDO $pdo;

    /**
     * @throws ConfigFileNotExistsException
     * @throws DatabaseConnectionException
     */
    public function __construct()
    {
        [$host, $port, $database, $username, $password] = $this->getConfigs();

        foreach (range(1, 5) as $try) {
            try {
                self::$pdo = new PDO("mysql:host={$host};port={$port};dbname={$database}", $username, $password);

                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
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
    private function getConfigs(): array
    {
        $host = Helper::getConfig('database.host');
        $port = Helper::getConfig('database.port');
        $database = Helper::getConfig('database.database');
        $username = Helper::getConfig('database.username');
        $password = Helper::getConfig('database.password');
        return array($host, $port, $database, $username, $password);
    }
}