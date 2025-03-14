<?php

namespace src\states;

use src\contracts\VendingMachineState;

class IDLEState extends VendingMachineState
{
    public function insertCoin(): void
    {
        echo "Coin accepted.\n";
    }

    public function ejectCoin(): void
    {
        echo "Returning coins.\n";
    }

    public function selectProduct(): void
    {
        echo "Product selected.\n";
        $this->vendingMachine->setState(new ProductSelectedState($this->vendingMachine));
    }

    public function dispense(): void
    {
        echo "Error: Please select a product first.\n";
    }
}