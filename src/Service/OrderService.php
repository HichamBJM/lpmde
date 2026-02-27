<?php

namespace App\Service;

use App\Entity\Order;
use App\Enum\OrderStatus;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class OrderService
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /** @return list<array<string, mixed>> */
    public function all(): array
    {
        $orders = $this->orderRepository->findAllNewestFirst();

        return array_map([$this, 'normalize'], $orders);
    }

    /** @param list<array{sku:string,name:string,price:float,quantity:int,subtotal:float}> $items */
    public function createFromCart(array $items, float $totalAmount, string $customerEmail): array
    {
        if ([] === $items) {
            throw new InvalidArgumentException('Le panier est vide.');
        }

        $now = new \DateTimeImmutable();

        $order = new Order();
        $order->setNumber('CMD-'.strtoupper(substr(bin2hex(random_bytes(4)), 0, 8)));
        $order->setStatus(OrderStatus::DRAFT);
        $order->setTotalAmount(round($totalAmount, 2));
        $order->setItems($items);
        $order->setCreatedAt($now);
        $order->setCustomerEmail($customerEmail);
        $order->setHistory([
            [
                'at' => $now->format(DATE_ATOM),
                'status' => OrderStatus::DRAFT,
                'label' => 'Panier transformé en commande',
            ],
        ]);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $this->normalize($order);
    }

    /** @return list<array<string, mixed>> */
    public function forCustomer(string $customerEmail): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn (array $order): bool => ($order['customerEmail'] ?? '') === $customerEmail
        ));
    }

    /** @return list<array<string, mixed>> */
    public function toProcess(): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn (array $order): bool => in_array(
                (string) ($order['status'] ?? ''),
                [OrderStatus::PENDING_PAYMENT, OrderStatus::PAID, OrderStatus::PREPARING],
                true
            )
        ));
    }

    public function find(string $number): ?array
    {
        $order = $this->orderRepository->findOneBy(['number' => $number]);

        return $order ? $this->normalize($order) : null;
    }

    public function transition(string $number, string $targetStatus, string $label): ?array
    {
        $order = $this->orderRepository->findOneBy(['number' => $number]);
        if (!$order) {
            return null;
        }

        $currentStatus = (string) $order->getStatus();
        if (!OrderStatus::canTransition($currentStatus, $targetStatus)) {
            throw new InvalidArgumentException(sprintf('Transition invalide : %s → %s', $currentStatus, $targetStatus));
        }

        $history = $order->getHistory();
        $history[] = [
            'at' => (new \DateTimeImmutable())->format(DATE_ATOM),
            'status' => $targetStatus,
            'label' => $label,
        ];

        $order->setStatus($targetStatus);
        $order->setHistory($history);

        $this->entityManager->flush();

        return $this->normalize($order);
    }

    /** @return array<string,mixed> */
    private function normalize(Order $order): array
    {
        return [
            'number' => $order->getNumber(),
            'status' => $order->getStatus(),
            'totalAmount' => $order->getTotalAmount(),
            'items' => $order->getItems(),
            'createdAt' => $order->getCreatedAt()?->format(DATE_ATOM),
            'customerEmail' => $order->getCustomerEmail(),
            'history' => $order->getHistory(),
        ];
    }
}
