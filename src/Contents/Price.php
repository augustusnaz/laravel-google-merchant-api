<?php

namespace MOIREI\GoogleMerchantApi\Contents;

use Closure;

class Price extends BaseContent
{

    /**
     * Allowed attributes.
     *
     * @var string|double   value
     * @var string          currency
     *
     * @var array
     */
    protected $allowed_attributes = [
        'value', 'currency',
    ];

	/**
	 * Setup the content price
	 */
    public function __construct(){

        parent::__construct();

        $this->attributes['currency'] = config('laravel-google-merchant-api.default_currency', 'AUD');

    }

}
