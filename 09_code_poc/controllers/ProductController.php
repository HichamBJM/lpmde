<?php

declare(strict_types=1);

namespace POC\Controllers;

use POC\Services\ProductService;

final class ProductController
{
    public function __construct(private ProductService $productService)
    {
    }

    /**
     * @return array{products: array<array{id:int,name:string,price:float}>}
     */
    public function listAction(): array
    {
        $products = $this->productService->getActiveProducts();

        return [
            'products' => array_map(
                static fn ($product): array => [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                ],
                $products,
            ),
        ];
    }

    /**
     * @param array<int,array{unitPrice:float,quantity:int}> $lines
     */
    public function cartTotalAction(array $lines): array
    {
        return ['total' => $this->productService->calculateCartTotal($lines)];
    }
}
