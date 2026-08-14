<?php

namespace App\Repository;

use App\Model\Tariff;
use PDO;

class TariffRepository
{
    public function __construct(
        private PDO $connection
    ) {
    }

    /**
     * @return Tariff[]
     */
    public function findAll(): array
    {
        $statement = $this->connection->query(
            'SELECT * FROM tariffs ORDER BY id DESC'
        );

        $tariffs = [];

        foreach ($statement->fetchAll() as $row) {
            $tariffs[] = $this->mapRowToTariff($row);
        }

        return $tariffs;
    }

    public function findById(int $id): ?Tariff
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM tariffs WHERE id = :id'
        );

        $statement->execute([
            'id' => $id,
        ]);

        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return $this->mapRowToTariff($row);
    }

    public function create(
        string $name,
        ?string $description,
        int $speed,
        float $price,
        string $createdAt,
        ?string $expiresAt,
    ): int {
        $statement = $this->connection->prepare(
            'INSERT INTO tariffs
                (name, description, speed, price, created_at, expires_at)
             VALUES
                (:name, :description, :speed, :price, :created_at, :expires_at)'
        );

        $statement->execute([
            'name' => $name,
            'description' => $description,
            'speed' => $speed,
            'price' => $price,
            'created_at' => $createdAt,
            'expires_at' => $expiresAt,
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function update(Tariff $tariff): void
    {
        $statement = $this->connection->prepare(
            'UPDATE tariffs
         SET name = :name,
             description = :description,
             speed = :speed,
             price = :price,
             expires_at = :expires_at
         WHERE id = :id'
        );

        $statement->execute([
            'id' => $tariff->id,
            'name' => $tariff->name,
            'description' => $tariff->description,
            'speed' => $tariff->speed,
            'price' => $tariff->price,
            'expires_at' => $tariff->expiresAt,
        ]);
    }

    private function mapRowToTariff(array $row): Tariff
    {
        return new Tariff(
            id: (int) $row['id'],
            name: $row['name'],
            description: $row['description'],
            speed: (int) $row['speed'],
            price: (float) $row['price'],
            createdAt: $row['created_at'],
            expiresAt: $row['expires_at'],
        );
    }
}
