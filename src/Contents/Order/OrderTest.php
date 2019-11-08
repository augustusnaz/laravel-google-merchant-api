<?php

namespace MOIREI\GoogleMerchantApi\Contents\Order;

use MOIREI\GoogleMerchantApi\Contents\BaseContent;
use Carbon\Carbon;

class OrderTest extends Order
{
	/**
	 * Setup the test order resource
     *
     * @var string  kind
     * @var string  notificationMode            'checkoutIntegration', 'merchantPull'
     * @var string  predefinedEmail             'pog.dwight.schrute@gmail.com', 'pog.jim.halpert@gmail.com', 'pog.pam.beesly@gmail.com'
     * @var string  predefinedDeliveryAddress   'dwight, 'jim', 'pam'
     * @var string  predefinedBillingAddress    'dwight, 'jim', 'pam'
     * @var bollean enableOrderinvoices
     * @var string  predefinedPickupDetails     'dwight', 'jim', 'pam'
     *
	 */
    public function __construct(){
        $this->attributes['kind'] = 'content#testOrder';

        $this->attributes[ 'lineItems' ] = array();
        $this->attributes[ 'promotions' ] = array();

        $this->allowed_attributes = array_merge($this->allowed_attributes, [
            'notificationMode',
            'country', 'predefinedEmail', 'predefinedDeliveryAddress', 'predefinedBillingAddress',
            'enableOrderinvoices', 'predefinedPickupDetails',
        ]);
    }

}
