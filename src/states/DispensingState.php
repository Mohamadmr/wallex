<?php

namespace src\states;

use src\contracts\VendingMachineState;

class DispensingState extends VendingMachineState
{
    public function insertCoin(): void
    {
        echo "Error: Please wait until product dispensing is complete.\n";
    }

    public function ejectCoin(): void
    {
        echo "Error: Product is being dispensed. Cannot return coin.\n";
    }

    public function selectProduct(): void
    {
        echo "Error: Please wait until product dispensing is complete.\n";
    }

    public function dispense(): void
    {
        echo "Product dispensed!\n";
        $this->vendingMachine->updateProductCount();
        $this->vendingMachine->unlockProduct();
        $this->vendingMachine->setState(new IDLEState($this->vendingMachine));
    }
}