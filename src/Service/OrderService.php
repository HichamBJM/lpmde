<?php

namespace App\Service;

use App\Enum\OrderStatus;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\RequestStack;

class OrderService
{
    private const ORDERS_KEY = 'orders';

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    /** @return list<array<string, mixed>> */
    public function all(): array
    {
        $orders = $this->requestStack->getSession()->get(self::ORDERS_KEY, []);

        usort(
            $orders,
            static fn (array $a, array $b): int => strcmp((string) $b['createdAt'], (string) $a['createdAt'])
        );

        return $orders;
    }

    /** @param list<array{sku:string,name:string,price:float,quantity:int,subtotal:float}> $items */
    public function createFromCart(array $items, float $totalAmount): array
    {
        if ([] === $items) {
            throw new InvalidArgumentException('Le panier est vide.');
        }

        $order = [
            'number' => 'CMD-'.strtoupper(substr(bin2hex(random_bytes(4)), 0, 8)),
            'status' => OrderStatus::DRAFT,
            'totalAmount' => round($totalAmount, 2),
            'items' => $items,
            'createdAt' => (new \DateTimeImmutable())->format(DATE_ATOM),
            'history' => [
                [
                    'at' => (new \DateTimeImmutable())->format(DATE_ATOM),
                    'status' => OrderStatus::DRAFT,
                    'label' => 'Panier transformé en commande',
                ],
            ],
        ];

        $orders = $this->all();
        $orders[] = $order;
        $this->save($orders);

        return $order;
    }

    public function find(string $number): ?array
    {
        foreach ($this->all() as $order) {
            if ($order['number'] === $number) {
                return $order;
            }
        }

        return null;
    }

    public function transition(string $number, string $targetStatus, string $label): ?array
    {
        $orders = $this->all();

        foreach ($orders as $index => $order) {
            $currentStatus = (string) $order['status'];
            if ($order['number'] !== $number) {
                continue;
            }

            if (!OrderStatus::canTransition($currentStatus, $targetStatus)) {
                throw new InvalidArgumentException(sprintf('Transition invalide : %s → %s', $currentStatus, $targetStatus));
            }

            $orders[$index]['status'] = $targetStatus;
            $orders[$index]['history'][] = [
                'at' => (new \DateTimeImmutable())->format(DATE_ATOM),
                'status' => $targetStatus,
                'label' => $label,
            ];

            $this->save($orders);

            return $orders[$index];
        }

        return null;
    }

    /** @param list<array<string,mixed>> $orders */
    private function save(array $orders): void
    {
        $this->requestStack->getSession()->set(self::ORDERS_KEY, $orders);
    }
}
