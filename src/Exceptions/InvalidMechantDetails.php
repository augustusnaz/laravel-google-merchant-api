<?php

namespace MOIREI\GoogleMerchantApi\Exceptions;

class InvalidMechantDetails extends \Exception
{
    protected $message = 'MOIREI\GoogleMerchantApi: Could not determine merchant credentials.';
}
