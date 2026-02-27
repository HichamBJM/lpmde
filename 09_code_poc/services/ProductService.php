<?php

declare(strict_types=1);

namespace POC\Services;

use POC\Models\Product;

final class ProductService
{
    /**
     * @return array<Product>
     */
    public function getActiveProducts(): array
    {
        $products = [
            new Product(1, 'Bougie hantée', 12.90, true),
            new Product(2, 'Mug du manoir', 9.90, true),
            new Product(3, 'Affiche édition limitée', 19.00, false),
        ];

        return array_values(array_filter(
            $products,
            static fn (Product $product): bool => $product->isActive(),
        ));
    }

    public function calculateCartTotal(array $lines): float
    {
        $total = 0.0;

        foreach ($lines as $line) {
            $total += (float) $line['unitPrice'] * (int) $line['quantity'];
        }

        if ($total > 100.0) {
            $total *= 0.9; // remise 10%
        }

        return round($total, 2);
    }
}
