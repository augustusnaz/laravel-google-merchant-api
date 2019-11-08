<?php

namespace MOIREI\GoogleMerchantApi\Exceptions;

class InvalidProductInput extends \Exception
{
    protected $message = 'MOIREI\GoogleMerchantApi: The product content type is invalid.';
}