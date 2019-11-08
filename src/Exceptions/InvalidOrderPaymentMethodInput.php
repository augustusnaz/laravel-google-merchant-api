<?php

namespace MOIREI\GoogleMerchantApi\Exceptions;

class InvalidOrderPaymentMethodInput extends \Exception
{
    protected $message = 'MOIREI\GoogleMerchantApi: The order payment method type is invalid.';
}