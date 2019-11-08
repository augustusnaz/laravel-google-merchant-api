<?php

namespace MOIREI\GoogleMerchantApi\Exceptions;

class InvalidProductShippingInput extends \Exception
{
    protected $message = 'MOIREI\GoogleMerchantApi: The product shipping content type is invalid.';
}