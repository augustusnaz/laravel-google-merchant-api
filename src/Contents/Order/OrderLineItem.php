<?php

namespace MOIREI\GoogleMerchantApi\Contents\Order;

use MOIREI\GoogleMerchantApi\Contents\BaseContent;
use MOIREI\GoogleMerchantApi\Contents\Price;

class OrderLineItem extends BaseContent
{

    /**
     * Names of alloed attributes
     *
     * @var string  id
     * @var unsignedinteger quantityOrdered
     * @var unsignedinteger quantityPending
     * @var unsignedinteger quantityShipped
     * @var unsignedinteger quantityDelivered
     * @var unsignedinteger quantityReturned
     * @var unsignedinteger quantityCanceled
     * @var unsignedinteger quantityUndeliverable
     * @var unsignedinteger quantityReadyForPickup
     * @var object  price
     * @var object  tax
     * @var object  adjustments
     * @var object  shippingDetails
     * @var object  returnInfo
     * @var object  product
     * @var array   returns
     * @var array   cancellations
     * @var array   annotations
     *
     * @var array
     */
    protected $allowed_attributes = [
        'id',
        'quantityOrdered', 'quantityPending', 'quantityShipped', 'quantityDelivered', 'quantityReturned', 'quantityCanceled',
        'quantityUndeliverable', 'quantityReadyForPickup', 'price', 'tax', 'adjustments', 'shippingDetails', 'returnInfo', 'product',
        'returns', 'cancellations', 'annotations',
    ];

    /**
     * Mutate the shipping details
     *
     * @return array
     */
    public function getShippingDetails(){
        return (new OrderShippingDetails)->with($this->attributes['shippingDetails'])->all();
    }

}
