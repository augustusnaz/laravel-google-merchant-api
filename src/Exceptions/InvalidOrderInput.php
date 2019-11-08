<?php

namespace MOIREI\GoogleMerchantApi\Exceptions;

class InvalidOrderInput extends \Exception
{
    protected $message = 'MOIREI\GoogleMerchantApi: The order content type is invalid.';
}