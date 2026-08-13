<?php
namespace App\Database;

use PDO;

class Database
{
    private PDO $connection;

    public function __construct(string $databasePath)
    {
        $this->connection = new PDO(
            'sqlite:' . $databasePath
        );

        $this->connection->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        $this->connection->setAttribute(
            PDO::ATTR_DEFAULT_FETCH_MODE,
            PDO::FETCH_ASSOC
        );
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
