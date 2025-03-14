<?php

namespace src\exceptions;

use Exception;
use Throwable;

class NotEnoughBalance extends Exception
{
    public function __construct(string $productUid)
    {
        parent::__construct("Not enough balance to purchase product: $productUid");
    }
}