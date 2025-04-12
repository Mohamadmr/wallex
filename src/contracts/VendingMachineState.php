<?php

namespace src\contracts;

use src\VendingMachine;

abstract class VendingMachineState
{
    public function __construct(protected VendingMachine $vendingMachine)
    {
    }

    abstract public function insertCoin(): void;
    abstract public function ejectCoin(): void;
    abstract public function selectProduct(): void;
    abstract public function dispense(): void;

    public function getVendingMachine(): VendingMachine
    {
        return $this->vendingMachine;
    }

    public function getStateName(): string
    {
        return $this->__toString();
    }

    public function __toString(): string
    {
        $stateName = explode('state', __CLASS__)[0];

        return $stateName.' '.'state';
    }
}