<?php

namespace App\Model;

class Tariff
{
    public function __construct(
        public readonly ?int $id,
        public string $name,
        public ?string $description,
        public int $speed,
        public float $price,
        public string $createdAt,
        public ?string $expiresAt,
    ) {
    }
}
