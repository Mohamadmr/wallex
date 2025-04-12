<?php

require __DIR__ . '/../vendor/autoload.php';

use src\SAGAWorker;
use src\exceptions\NotEnoughBalance;
use src\exceptions\NotEnoughCount;
use src\exceptions\ProductNotFound;

$worker = new SAGAWorker();
$vendingMachine = $worker->getFirstIDLEVendingMachine();

try {
    $vendingMachine->insertCoin(10);
    $vendingMachine->selectProduct('soda_uid');
    $vendingMachine->dispense();
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Product dispensed successfully'
    ]);
} catch (ProductNotFound $exception) {
    echo json_encode([
        'status' => 'error',
        'message' => $exception->getMessage()
    ]);
} catch (NotEnoughCount|NotEnoughBalance $exception) {
    echo json_encode([
        'status' => 'error',
        'message' => $exception->getMessage()
    ]);
} finally {
    $vendingMachine->ejectCoin();

    $worker->dropIDLEVendingMachines();
}
