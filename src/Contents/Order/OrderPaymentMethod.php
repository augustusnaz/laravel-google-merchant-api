<?php

namespace MOIREI\GoogleMerchantApi\Contents\Order;

use MOIREI\GoogleMerchantApi\Contents\BaseContent;

class OrderPaymentMethod extends BaseContent
{

    /**
     * Allowed attributes.
     *
     * @var string   type
     * @var string   lastFourDigits
     * @var string   lastFourDigits
     * @var integer  lastFourDigits
     * @var integer  expirationYear
     *
     * @var array
     */
    protected $allowed_attributes = [
        'type', 'lastFourDigits', 'predefinedBillingAddress',
        'expirationMonth', 'expirationYear',
    ];
}
