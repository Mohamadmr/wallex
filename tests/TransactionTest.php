<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use src\Transaction;

class TransactionTest extends TestCase
{
    private Transaction $transaction;
    
    protected function setUp(): void
    {
        $this->transaction = new Transaction();
    }
    
    public function testGetProduct(): void
    {
        $this->assertIsArray($this->transaction->getProduct('soda_uid'));
        $this->assertNull($this->transaction->getProduct('invalid_uid'));
    }
    
    public function testGetCount(): void
    {
        $this->assertEquals(5, $this->transaction->getCount('soda_uid'));
        $this->assertEquals(0, $this->transaction->getCount('invalid_uid'));
    }
    
    public function testLockUnlockProduct(): void
    {
        $this->transaction->lockProduct('soda_uid');
        $product = $this->transaction->getProduct('soda_uid');
        $this->assertTrue($product['lock']);
        
        $this->transaction->unlockProduct('soda_uid');
        $product = $this->transaction->getProduct('soda_uid');
        $this->assertFalse($product['lock']);
    }
    
    public function testDecreaseProductCount(): void
    {
        $initialCount = $this->transaction->getCount('soda_uid');
        $this->transaction->decreaseProductCount('soda_uid');
        $this->assertEquals($initialCount - 1, $this->transaction->getCount('soda_uid'));
    }
    
    public function testDecreaseProductCountWithInvalidProduct(): void
    {
        $this->transaction->decreaseProductCount('invalid_uid');
        $this->assertTrue(true);
    }
    
    public function testSetProduct(): void
    {
        $newProduct = ['count' => 10, 'cost' => 20, 'lock' => false];
        $this->transaction->setProduct('new_product', $newProduct);
        $this->assertEquals($newProduct, $this->transaction->getProduct('new_product'));
    }
    
    public function testGetProducts(): void
    {
        $products = $this->transaction->getProducts();
        $this->assertIsArray($products);
        $this->assertArrayHasKey('soda_uid', $products);
        $this->assertArrayHasKey('cafe_uid', $products);
    }
    
    public function testLockUnlockInvalidProduct(): void
    {
        $this->transaction->lockProduct('invalid_uid');
        $this->transaction->unlockProduct('invalid_uid');
        $this->assertTrue(true);
    }
} 