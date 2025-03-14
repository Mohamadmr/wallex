<?php

namespace src;

use src\contracts\TransactionInterface;
use src\contracts\VendingMachineState;
use src\exceptions\NotEnoughBalance;
use src\exceptions\NotEnoughCount;
use src\exceptions\ProductNotFound;
use src\states\IDLEState;

class VendingMachine
{
    private float $balance = 0;
    private ?array $product = null;
    private string $productUid = '';
    private VendingMachineState $currentState;

    public function __construct(private readonly TransactionInterface $transaction)
    {
        $this->currentState = new IDLEState($this);
    }

    public function insertCoin(float $amount): void
    {
        $this->setBalance($this->balance + $amount);

        $this->currentState->insertCoin();
    }

    public function ejectCoin(): void
    {
        $this->setBalance(0);

        $this->currentState->ejectCoin();
    }

    /**
     * @throws ProductNotFound
     * @throws NotEnoughCount
     * @throws NotEnoughBalance
     */
    public function selectProduct(string $productUid): void
    {
        $this->product = $this->transaction->getProduct($productUid);

        if ($this->product === null) {
            throw new ProductNotFound($productUid);
        }

        if ($this->transaction->getCount($productUid) === 0) {
            throw new NotEnoughCount($productUid);
        }

        if ($this->balance < $this->product['cost']) {
            throw new NotEnoughBalance($productUid);
        }

        while ($this->product['lock']) {
            usleep(500);
        }

        $this->transaction->lockProduct($productUid);
        $this->setProductUid($productUid);
        $this->balance -= $this->product['cost'];

        $this->currentState->selectProduct();
    }

    public function dispense(): void
    {
        $this->currentState->dispense();
    }

    public function updateProductCount(): void
    {
        $this->transaction->decreaseProductCount($this->productUid);
    }

    public function unlockProduct(): void
    {
        $this->transaction->unlockProduct($this->productUid);
    }

    public function setState(VendingMachineState $state): void
    {
        $this->currentState = $state;
    }

    public function getCurrentState(): VendingMachineState
    {
        return $this->currentState;
    }

    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setProductUid(string $productUid): void
    {
        $this->productUid = $productUid;
    }

    public function getProductUid(): string
    {
        return $this->productUid;
    }

    public function getProduct(): ?array
    {
        return $this->product;
    }

    public function getTransaction(): TransactionInterface
    {
        return $this->transaction;
    }
}