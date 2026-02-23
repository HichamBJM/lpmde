<?php

namespace App\Tests\Unit;

use App\Enum\OrderStatus;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    public function testAllowedTransitions(): void
    {
        $this->assertTrue(OrderStatus::canTransition(OrderStatus::DRAFT, OrderStatus::PENDING_PAYMENT));
        $this->assertTrue(OrderStatus::canTransition(OrderStatus::PAID, OrderStatus::PREPARING));
        $this->assertTrue(OrderStatus::canTransition(OrderStatus::SHIPPED, OrderStatus::DELIVERED));
    }

    public function testRejectedTransitions(): void
    {
        $this->assertFalse(OrderStatus::canTransition(OrderStatus::DRAFT, OrderStatus::DELIVERED));
        $this->assertFalse(OrderStatus::canTransition(OrderStatus::DELIVERED, OrderStatus::PREPARING));
        $this->assertFalse(OrderStatus::canTransition(OrderStatus::CANCELED, OrderStatus::PAID));
    }
}
