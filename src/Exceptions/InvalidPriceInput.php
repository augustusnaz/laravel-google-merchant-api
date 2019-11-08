<?php

namespace MOIREI\GoogleMerchantApi\Exceptions;

class InvalidPriceInput extends \Exception
{
    protected $message = 'MOIREI\GoogleMerchantApi: The content price type is invalid.';
}