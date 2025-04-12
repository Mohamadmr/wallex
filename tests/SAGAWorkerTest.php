<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use src\SAGAWorker;
use src\states\DispensingState;
use src\states\IDLEState;
use src\states\ProductSelectedState;
use src\VendingMachine;

class SAGAWorkerTest extends TestCase
{
    private SAGAWorker $sagaWorker;
    
    protected function setUp(): void
    {
        $this->sagaWorker = new SAGAWorker();
    }
    
    public function testConstructorCreatesOneVendingMachine(): void
    {
        $vendingMachines = $this->sagaWorker->getVendingMachines();
        $this->assertCount(1, $vendingMachines);
        $this->assertInstanceOf(VendingMachine::class, $vendingMachines[0]);
    }
    
    public function testAddVendingMachine(): void
    {
        $initialCount = count($this->sagaWorker->getVendingMachines());
        $newMachine = $this->sagaWorker->addVendingMachine();
        
        $this->assertInstanceOf(VendingMachine::class, $newMachine);
        $this->assertCount($initialCount + 1, $this->sagaWorker->getVendingMachines());
    }
    
    public function testGetFirstIDLEVendingMachine(): void
    {
        // همه دستگاه‌ها در ابتدا در حالت IDLE هستند
        $machine = $this->sagaWorker->getFirstIDLEVendingMachine();
        $this->assertInstanceOf(VendingMachine::class, $machine);
        $this->assertInstanceOf(IDLEState::class, $machine->getCurrentState());
    }
    
    public function testGetFirstIDLEVendingMachineCreatesNewWhenNoneAvailable(): void
    {
        // حذف تمام ماشین‌های موجود
        $this->sagaWorker->dropIDLEVendingMachines();
        
        // بررسی اینکه ماشین جدیدی ایجاد می‌شود
        $machine = $this->sagaWorker->getFirstIDLEVendingMachine();
        $this->assertInstanceOf(VendingMachine::class, $machine);
        $this->assertInstanceOf(IDLEState::class, $machine->getCurrentState());
        
        // بررسی اینکه ماشین جدید به لیست اضافه شده است
        $this->assertCount(1, $this->sagaWorker->getVendingMachines());
    }
    
    public function testDropIDLEVendingMachines(): void
    {
        // اضافه کردن چند ماشین جدید
        $this->sagaWorker->addVendingMachine();
        $this->sagaWorker->addVendingMachine();
        
        // تغییر وضعیت یکی از ماشین‌ها به ProductSelected
        $machines = $this->sagaWorker->getVendingMachines();

        $machines[1]->insertCoin(10);
        $machines[1]->selectProduct('soda_uid');
        
        // حذف ماشین‌های IDLE
        $this->sagaWorker->dropIDLEVendingMachines();
        
        // بررسی اینکه فقط ماشین‌های غیر IDLE باقی مانده‌اند
        $remainingMachines = $this->sagaWorker->getVendingMachines();
        $this->assertCount(1, $remainingMachines);
        $this->assertInstanceOf(ProductSelectedState::class, $remainingMachines[0]->getCurrentState());
    }
    
    public function testMultipleVendingMachinesStates(): void
    {
        // اضافه کردن چند ماشین
        $this->sagaWorker->addVendingMachine();
        $this->sagaWorker->addVendingMachine();
        
        $machines = $this->sagaWorker->getVendingMachines();
        
        // تغییر وضعیت ماشین‌ها
        $machines[0]->insertCoin(10);
        $machines[0]->selectProduct('soda_uid');

        $machines[1]->insertCoin(15);
        $machines[1]->selectProduct('cafe_uid');

        // حذف ماشین‌های IDLE
        $this->sagaWorker->dropIDLEVendingMachines();
        
        // بررسی اینکه همه ماشین‌ها در وضعیت ProductSelected هستند
        $remainingMachines = $this->sagaWorker->getVendingMachines();
        foreach ($remainingMachines as $machine) {
            $this->assertInstanceOf(ProductSelectedState::class, $machine->getCurrentState());
        }
    }
} 