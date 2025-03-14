<?php

namespace src\exceptions;

use Exception;
use Throwable;

class ProductNotFound extends Exception
{
    public function __construct(string $productUid)
    {
        parent::__construct("Product not found: $productUid");
    }
}