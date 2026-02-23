<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private const CART_KEY = 'cart';

    /** @var array<string, array{name:string,price:float}> */
    private const CATALOG = [
        'haunted-castle-collector' => ['name' => 'Haunted Castle Collector Set', 'price' => 120.00],
        'ancient-totem-figurine' => ['name' => 'Ancient Totem Figurine', 'price' => 85.00],
        'dark-rituals-board-game' => ['name' => 'Dark Rituals Board Game', 'price' => 45.00],
        'ghostly-manifestation-art' => ['name' => 'Ghostly Manifestation Art', 'price' => 60.00],
    ];

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    /** @return array<string, int> */
    public function getRawCart(): array
    {
        return $this->requestStack->getSession()->get(self::CART_KEY, []);
    }

    public function add(string $sku): void
    {
        if (!isset(self::CATALOG[$sku])) {
            return;
        }

        $cart = $this->getRawCart();
        $cart[$sku] = ($cart[$sku] ?? 0) + 1;
        $this->requestStack->getSession()->set(self::CART_KEY, $cart);
    }

    public function updateQuantity(string $sku, int $quantity): void
    {
        $cart = $this->getRawCart();

        if ($quantity <= 0) {
            unset($cart[$sku]);
        } elseif (isset(self::CATALOG[$sku])) {
            $cart[$sku] = $quantity;
        }

        $this->requestStack->getSession()->set(self::CART_KEY, $cart);
    }

    public function remove(string $sku): void
    {
        $cart = $this->getRawCart();
        unset($cart[$sku]);
        $this->requestStack->getSession()->set(self::CART_KEY, $cart);
    }

    public function clear(): void
    {
        $this->requestStack->getSession()->set(self::CART_KEY, []);
    }

    /** @return list<array{sku:string,name:string,price:float,quantity:int,subtotal:float}> */
    public function getDetailedItems(): array
    {
        $items = [];
        foreach ($this->getRawCart() as $sku => $quantity) {
            if (!isset(self::CATALOG[$sku])) {
                continue;
            }

            $product = self::CATALOG[$sku];
            $items[] = [
                'sku' => $sku,
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'subtotal' => $product['price'] * $quantity,
            ];
        }

        return $items;
    }

    public function totalAmount(): float
    {
        return array_reduce(
            $this->getDetailedItems(),
            static fn (float $carry, array $item): float => $carry + $item['subtotal'],
            0.0
        );
    }

    public function totalQuantity(): int
    {
        return array_sum($this->getRawCart());
    }

    /** @return array<string, array{name:string,price:float}> */
    public function catalog(): array
    {
        return self::CATALOG;
    }
}
