<?php

namespace tests\states;

use PHPUnit\Framework\TestCase;
use src\states\IDLEState;
use src\states\ProductSelectedState;
use src\VendingMachine;

class IDLEStateTest extends TestCase
{
    private VendingMachine $vendingMachine;
    private IDLEState $idleState;
    
    protected function setUp(): void
    {
        $this->vendingMachine = $this->getMockBuilder(VendingMachine::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->idleState = new IDLEState($this->vendingMachine);
    }
    
    public function testSelectProduct(): void
    {
        $this->vendingMachine->expects($this->once())
            ->method('setState')
            ->with($this->callback(function ($state) {
                return $state instanceof ProductSelectedState;
            }));
        
        // استفاده از buffer برای جلوگیری از چاپ خروجی در تست
        ob_start();
        $this->idleState->selectProduct();
        ob_end_clean();
    }
    
    public function testInsertCoin(): void
    {
        // فقط تست می‌کنیم که خطایی رخ نمی‌دهد
        ob_start();
        $this->idleState->insertCoin();
        $output = ob_get_clean();
        
        $this->assertStringContainsString("Coin accepted", $output);
    }
    
    public function testEjectCoin(): void
    {
        ob_start();
        $this->idleState->ejectCoin();
        $output = ob_get_clean();
        
        $this->assertStringContainsString("Returning coins", $output);
    }
    
    public function testDispense(): void
    {
        ob_start();
        $this->idleState->dispense();
        $output = ob_get_clean();
        
        $this->assertStringContainsString("Error", $output);
        $this->assertStringContainsString("select a product first", $output);
    }
    
    public function testVendingMachineReference(): void
    {
        $this->assertSame($this->vendingMachine, $this->idleState->getVendingMachine());
    }
} 