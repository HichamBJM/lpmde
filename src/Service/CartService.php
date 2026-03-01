<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private const CART_KEY = 'cart';

    /** @var array<string, array{name:string,price:float,description:string}> */
    private const DEFAULT_CATALOG = [
        'haunted-castle-collector' => ['name' => 'Haunted Castle Collector Set', 'price' => 120.00, 'description' => 'Collection premium château hanté'],
        'ancient-totem-figurine' => ['name' => 'Ancient Totem Figurine', 'price' => 85.00, 'description' => 'Figurine totem ancien'],
        'dark-rituals-board-game' => ['name' => 'Dark Rituals Board Game', 'price' => 45.00, 'description' => 'Jeu de plateau dark rituals'],
        'ghostly-manifestation-art' => ['name' => 'Ghostly Manifestation Art', 'price' => 60.00, 'description' => 'Affiche collection manifestation fantôme'],
    ];

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /** @return array<string, int> */
    public function getRawCart(): array
    {
        return $this->requestStack->getSession()->get(self::CART_KEY, []);
    }

    public function add(string $sku): void
    {
        $catalog = $this->catalog();
        if (!isset($catalog[$sku])) {
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
        } elseif (isset($this->catalog()[$sku])) {
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
        $catalog = $this->catalog();
        $items = [];

        foreach ($this->getRawCart() as $sku => $quantity) {
            if (!isset($catalog[$sku])) {
                continue;
            }

            $product = $catalog[$sku];
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
        $this->ensureCatalogSeeded();

        $catalog = [];
        foreach ($this->productRepository->findActiveOrderedByName() as $product) {
            $sku = (string) $product->getSku();
            $catalog[$sku] = [
                'name' => (string) $product->getName(),
                'price' => (float) $product->getPrice(),
            ];
        }

        return $catalog;
    }

    private function ensureCatalogSeeded(): void
    {
        if ($this->productRepository->count([]) > 0) {
            return;
        }

        foreach (self::DEFAULT_CATALOG as $sku => $data) {
            $product = new Product();
            $product->setSku($sku);
            $product->setName($data['name']);
            $product->setDescription($data['description']);
            $product->setPrice($data['price']);
            $product->setActive(true);
            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();
    }
}
