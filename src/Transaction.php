<?php

namespace src;


use src\contracts\TransactionInterface;

class Transaction implements TransactionInterface
{
    protected array $products = [
        'soda_uid' => ['count' => 5, 'cost' => 10, 'lock' => false],
        'cafe_uid' => ['count' => 5, 'cost' => 15, 'lock' => false],
    ];

    public function getCount(string $productUid): int
    {
        return $this->products[$productUid]['count'] ?? 0;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function getProduct(string $uid): ?array
    {
        return $this->products[$uid] ?? null;
    }

    public function setProduct(string $productUid, array $data): void
    {
        $this->products[$productUid] = $data;
    }

    public function lockProduct(string $uid): void
    {
        if (isset($this->products[$uid])) {
            $this->products[$uid]['lock'] = true;
        }
    }

    public function unlockProduct(string $uid): void
    {
        if (isset($this->products[$uid])) {
            $this->products[$uid]['lock'] = false;
        }
    }

    public function decreaseProductCount(string $uid): void
    {
        if (isset($this->products[$uid]) && $this->products[$uid]['count'] > 0) {
            $this->products[$uid]['count']--;
        }
    }
}