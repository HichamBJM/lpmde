<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'customer_order')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32, unique: true)]
    private ?string $number = null;

    #[ORM\Column(length: 40)]
    private ?string $status = null;

    #[ORM\Column]
    private ?float $totalAmount = null;

    #[ORM\Column(length: 180)]
    private ?string $customerEmail = null;

    /** @var list<array{sku:string,name:string,price:float,quantity:int,subtotal:float}> */
    #[ORM\Column(type: 'json')]
    private array $items = [];

    /** @var list<array{at:string,status:string,label:string}> */
    #[ORM\Column(type: 'json')]
    private array $history = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): static
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }

    public function setCustomerEmail(string $customerEmail): static
    {
        $this->customerEmail = $customerEmail;

        return $this;
    }

    /** @return list<array{sku:string,name:string,price:float,quantity:int,subtotal:float}> */
    public function getItems(): array
    {
        return $this->items;
    }

    /** @param list<array{sku:string,name:string,price:float,quantity:int,subtotal:float}> $items */
    public function setItems(array $items): static
    {
        $this->items = $items;

        return $this;
    }

    /** @return list<array{at:string,status:string,label:string}> */
    public function getHistory(): array
    {
        return $this->history;
    }

    /** @param list<array{at:string,status:string,label:string}> $history */
    public function setHistory(array $history): static
    {
        $this->history = $history;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
