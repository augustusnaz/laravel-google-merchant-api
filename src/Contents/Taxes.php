<?php

namespace MOIREI\GoogleMerchantApi\Contents;

use Closure;

class Taxes extends BaseContent
{

    /**
     * Allowed attributes.
     *
     * @var string  country
     * @var long    locationId
     * @var string  postalCode
     * @var double  rate
     * @var string  region
     * @var boolean taxShip
     *
     * @var array
     */
    protected $allowed_attributes = [
        'country', 'locationId', 'postalCode', 'rate',
        'region', 'taxShip',
    ];

}
