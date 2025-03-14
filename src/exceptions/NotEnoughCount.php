<?php

namespace src\exceptions;

use Exception;
use Throwable;

class NotEnoughCount extends Exception {

    public function __construct(string $productUid)
    {
        parent::__construct("Product out of stock: $productUid");
    }
}