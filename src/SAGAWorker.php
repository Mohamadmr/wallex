<?php

namespace src;

use src\contracts\TransactionInterface;
use src\states\DispensingState;
use src\states\IDLEState;
use src\states\ProductSelectedState;

class SAGAWorker
{
    private array $vendingMachines;

    private TransactionInterface $transaction;

    public function __construct()
    {
        $this->transaction = new Transaction();
        $this->vendingMachines = [];
        $this->vendingMachines[] = new VendingMachine($this->transaction);
    }

    public function addVendingMachine(): VendingMachine
    {
        $this->vendingMachines[] = $vendingMachine = new VendingMachine($this->transaction);

        return $vendingMachine;
    }

    public function dropIDLEVendingMachines(): void
    {
        $workingVendingMachine = [];

        foreach ($this->vendingMachines as $index => $vendingMachine) {
            /* @var VendingMachine $vendingMachine */
            if (
                $vendingMachine->getCurrentState() instanceof ProductSelectedState ||
                $vendingMachine->getCurrentState() instanceof DispensingState
            ) {
                $workingVendingMachine[] = $vendingMachine;
            }
        }

        $this->vendingMachines = $workingVendingMachine;
    }

    public function getFirstIDLEVendingMachine(): VendingMachine
    {
        foreach ($this->vendingMachines as $vendingMachine) {
            if ($vendingMachine->getCurrentState() instanceof IDLEState) {
                return $vendingMachine;
            }
        }

        return $this->addVendingMachine();
    }
    
    public function getVendingMachines(): array
    {
        return $this->vendingMachines;
    }
}
