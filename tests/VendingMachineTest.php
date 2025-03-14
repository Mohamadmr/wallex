<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use src\contracts\TransactionInterface;
use src\exceptions\NotEnoughBalance;
use src\exceptions\NotEnoughCount;
use src\exceptions\ProductNotFound;
use src\states\DispensingState;
use src\states\IDLEState;
use src\states\ProductSelectedState;
use src\Transaction;
use src\VendingMachine;

class VendingMachineTest extends TestCase
{
    private VendingMachine $vendingMachine;
    private TransactionInterface $transaction;
    
    protected function setUp(): void
    {
        $this->transaction = $this->getMockBuilder(Transaction::class)
            ->getMock();
        
        $this->vendingMachine = new VendingMachine($this->transaction);
    }
    
    public function testInitialState(): void
    {
        $this->assertInstanceOf(IDLEState::class, $this->vendingMachine->getCurrentState());
    }
    
    public function testInsertCoin(): void
    {
        $this->vendingMachine->insertCoin(10);
        $this->assertEquals(10, $this->vendingMachine->getBalance());
        
        $this->vendingMachine->insertCoin(5);
        $this->assertEquals(15, $this->vendingMachine->getBalance());
    }
    
    public function testEjectCoin(): void
    {
        $this->vendingMachine->insertCoin(10);
        $this->assertEquals(10, $this->vendingMachine->getBalance());
        
        $this->vendingMachine->ejectCoin();
        $this->assertEquals(0, $this->vendingMachine->getBalance());
    }
    
    public function testSelectProductWithProductNotFound(): void
    {
        $this->transaction->method('getProduct')
            ->willReturn(null);
        
        $this->expectException(ProductNotFound::class);
        $this->vendingMachine->selectProduct('invalid_product');
    }
    
    public function testSelectProductWithNotEnoughCount(): void
    {
        $this->transaction->method('getProduct')
            ->willReturn(['count' => 0, 'cost' => 10, 'lock' => false]);
        
        $this->transaction->method('getCount')
            ->willReturn(0);
        
        $this->expectException(NotEnoughCount::class);
        $this->vendingMachine->selectProduct('out_of_stock_product');
    }
    
    public function testSelectProductWithNotEnoughBalance(): void
    {
        $this->transaction->method('getProduct')
            ->willReturn(['count' => 5, 'cost' => 20, 'lock' => false]);
        
        $this->transaction->method('getCount')
            ->willReturn(5);
        
        $this->vendingMachine->insertCoin(10);
        
        $this->expectException(NotEnoughBalance::class);
        $this->vendingMachine->selectProduct('expensive_product');
    }
    
    public function testSuccessfulProductSelection(): void
    {
        $this->transaction->method('getProduct')
            ->willReturn(['count' => 5, 'cost' => 10, 'lock' => false]);
        
        $this->transaction->method('getCount')
            ->willReturn(5);
        
        $this->transaction->expects($this->once())
            ->method('lockProduct');
        
        $this->vendingMachine->insertCoin(20);
        $this->vendingMachine->selectProduct('soda_uid');
        
        $this->assertInstanceOf(ProductSelectedState::class, $this->vendingMachine->getCurrentState());
        $this->assertEquals(10, $this->vendingMachine->getBalance());
    }
    
    public function testDispense(): void
    {
        // تنظیم وضعیت اولیه
        $this->transaction->method('getProduct')
            ->willReturn(['count' => 5, 'cost' => 10, 'lock' => false]);
        
        $this->transaction->method('getCount')
            ->willReturn(5);
        
        // اضافه کردن سکه و انتخاب محصول
        $this->vendingMachine->insertCoin(20);
        $this->vendingMachine->selectProduct('soda_uid');
        
        // تنظیم انتظارات برای Transaction
        $this->transaction->expects($this->once())
            ->method('decreaseProductCount')
            ->with('soda_uid');
        
        $this->transaction->expects($this->once())
            ->method('unlockProduct')
            ->with('soda_uid');
        
        // اجرای عملیات dispense
        $this->vendingMachine->dispense();
        
        // بررسی تغییر وضعیت به DispensingState
        $this->assertInstanceOf(DispensingState::class, $this->vendingMachine->getCurrentState());
        
        // بررسی موجودی نهایی
        $this->assertEquals(10, $this->vendingMachine->getBalance());
        
        // بررسی محصول انتخاب شده
        $this->assertEquals('soda_uid', $this->vendingMachine->getProductUid());
    }
    
    public function testProductLocking(): void
    {
        $this->transaction->method('getProduct')
            ->willReturn(['count' => 5, 'cost' => 10, 'lock' => true]);
        
        $this->transaction->method('getCount')
            ->willReturn(5);
        
        $this->vendingMachine->insertCoin(20);
        
        // انتظار داریم که قفل محصول آزاد شود
        $this->transaction->expects($this->once())
            ->method('unlockProduct')
            ->with('soda_uid');
        
        $this->vendingMachine->selectProduct('soda_uid');
    }
    
    public function testTransactionInterface(): void
    {
        $this->assertInstanceOf(TransactionInterface::class, $this->vendingMachine->getTransaction());
    }
} 