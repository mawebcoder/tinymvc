<?php

namespace System\Database\Driver;

use System\Helper\Helper;

use System\Exceptions\DatabaseConnectionException;
use System\Exceptions\ConfigFileNotExistsException;
use PDO;
use Exception;
class PDODriver
{

    public ?PDO $pdo = null;

    /**
     * @throws ConfigFileNotExistsException
     * @throws DatabaseConnectionException
     */
    public function __construct()
    {

        if (!is_null($this->pdo)) {
            return;
        }

        [
            'host' => $host,
            'port' => $port,
            'database' => $database,
            'username' => $username,
            'password' => $password
        ] = $this->getConfigs();

        foreach (range(1, 5) as $try) {
            try {
                $this->pdo = new PDO(
                    "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4",
                    $username,
                    $password
                );

                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
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
    private function getConfigs(): array
    {
        $host = Helper::getConfig('database.host');
        $port = Helper::getConfig('database.port');
        $database = Helper::getConfig('database.database');
        $username = Helper::getConfig('database.username');
        $password = Helper::getConfig('database.password');
        return [
            'host' => $host,
            'port' => $port,
            'database' => $database,
            'username' => $username,
            'password' => $password
        ];
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}