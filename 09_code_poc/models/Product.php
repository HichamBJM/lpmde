<?php

declare(strict_types=1);

namespace POC\Models;

final class Product
{
    public function __construct(
        private int $id,
        private string $name,
        private float $price,
        private bool $isActive = true,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
