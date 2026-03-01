<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use POC\Services\ProductService;

final class ProductServiceTest extends TestCase
{
    public function testCalculateCartTotalWithDiscount(): void
    {
        $service = new ProductService();

        $total = $service->calculateCartTotal([
            ['unitPrice' => 60.0, 'quantity' => 2],
        ]);

        self::assertSame(108.0, $total);
    }

    public function testGetActiveProductsReturnsOnlyActive(): void
    {
        $service = new ProductService();
        $products = $service->getActiveProducts();

        self::assertCount(2, $products);
    }
}
