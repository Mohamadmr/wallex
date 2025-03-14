<?php

namespace src\states;

use src\contracts\VendingMachineState;

class ProductSelectedState extends VendingMachineState
{
    public function insertCoin(): void
    {
        echo "Error: Product already selected. Please complete the transaction.\n";
    }

    public function ejectCoin(): void
    {
        echo "Returning coins. Transaction cancelled.\n";
        $this->vendingMachine->unlockProduct();
        $this->vendingMachine->setState(new IDLEState($this->vendingMachine));
    }

    public function selectProduct(): void
    {
        echo "Error: Product already selected. Please complete the current transaction.\n";
    }

    public function dispense(): void
    {
        echo "Dispensing product...\n";
        $this->vendingMachine->setState(new DispensingState($this->vendingMachine));
    }
}