<?php

namespace App\Message;

class ShippingRequested
{
    public function __construct(private readonly string $orderNumber)
    {
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }
}
