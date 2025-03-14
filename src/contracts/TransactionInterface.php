<?php

namespace src\contracts;

interface TransactionInterface
{
    public function getCount(string $productUid): int;

    public function getProducts(): array;

    public function getProduct(string $uid): ?array;

    public function setProduct(string $productUid, array $data): void;

    public function lockProduct(string $uid): void;

    public function unlockProduct(string $uid): void;

    public function decreaseProductCount(string $uid): void;
}