<?php

namespace MOIREI\GoogleMerchantApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \MOIREI\GoogleMerchantApi\Classes\ProductsApi
 */
class ProductApi extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'productApi';
    }
}
