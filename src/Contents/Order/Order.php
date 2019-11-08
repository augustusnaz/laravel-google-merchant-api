<?php

namespace MOIREI\GoogleMerchantApi\Contents\Order;

use MOIREI\GoogleMerchantApi\Contents\BaseContent;
use MOIREI\GoogleMerchantApi\Contents\Price;

class Order extends BaseContent
{

    /**
     * Names of alloed attributes
     *
     * @var string  kind
     * @var string  id
     * @var unsignedlong merchantId
     * @var string  merchantOrderId
     * @var string  channelType
     * @var array   lineItems
     * @var string  status
     * @var string  paymentStatus
     * @var boolean acknowledged
     * @var string  shippingOption
     * @var string  placedDate
     * @var object  deliveryDetails
     * @var object  pickupDetails
     * @var object  customer
     * @var object  paymentMethod
     * @var object  shippingCost
     * @var object  shippingCostTax
     * @var object  netAmount
     * @var array   refunds
     * @var array   shipments
     * @var array   promotions
     * @var string  taxCollector
     *
     * @var array
     */
    protected $allowed_attributes = [
        'kind',
        'id', 'merchantId', 'merchantOrderId', 'channelType', 'lineItems', 'status', 'paymentStatus',
        'acknowledged', 'shippingOption', 'placedDate', 'deliveryDetails', 'pickupDetails', 'customer', 'paymentMethod',
        'shippingCost', 'shippingCostTax', 'netAmount', 'refunds', 'shipments', 'promotions', 'taxCollector',

        'operationId',
    ];


	/**
	 * Setup the order content
	 */
    public function __construct(){
        $this->attributes['kind'] = 'content#order';

        $this->attributes['operationId'] = time();
    }

    /**
     * Set the order's shipping cost.
     *
     * @param  Closure|string|float|array $cost
     * @param  string|null $currency
     * @return $this
     */
    public function shippingCost($cost, $currency = null)
    {
        if(is_numeric($cost) || is_string($cost)){
            if(is_null($currency)){
                $currency = config('laravel-google-merchant-api.default_currency', 'AUD');
            }
            $cost = (new Price)->value($cost)->currency($currency);
        }elseif(is_array($cost)){
            $cost = (new Price)->with($cost);
        }elseif (is_callable($cost)) {
            $callback = $cost;
            $callback($cost = new Price);
        }elseif(!($cost instanceof Price)){
            throw new \MOIREI\GoogleMerchantApi\Exceptions\InvalidPriceInput;
        }

        $this->attributes[ 'shippingCost' ] = $cost->get();

        return $this;
    }

    /**
     * Set the order's shipping cost tax.
     *
     * @param  Closure|string|float|array $cost
     * @param  string|null $currency
     * @return $this
     */
    public function shippingCostTax($cost, $currency = null)
    {
        if(is_numeric($cost) || is_string($cost)){
            if(is_null($currency)){
                $currency = config('laravel-google-merchant-api.default_currency', 'AUD');
            }
            $cost = (new Price)->value($cost)->currency($currency);
        }elseif(is_array($cost)){
            $cost = (new Price)->with($cost);
        }elseif (is_callable($cost)) {
            $callback = $cost;
            $callback($cost = new Price);
        }elseif(!($cost instanceof Price)){
            throw new \MOIREI\GoogleMerchantApi\Exceptions\InvalidPriceInput;
        }

        $this->attributes[ 'shippingCostTax' ] = $cost->get();

        return $this;
    }

    /**
     * Set the order's payment method
     *
     * @param  Closure|array $paymentMethod
     * @return $this
     */
    public function paymentMethod($paymentMethod)
    {
        if(is_array($paymentMethod)){
            $paymentMethod = (new OrderPaymentMethod)->with($paymentMethod);
        }elseif (is_callable($paymentMethod)) {
            $callback = $paymentMethod;
            $callback($paymentMethod = new OrderPaymentMethod);
        }elseif(!($paymentMethod instanceof OrderPaymentMethod)){
            throw new \MOIREI\GoogleMerchantApi\Exceptions\InvalidOrderPaymentMethodInput;
        }

        $this->attributes[ 'paymentMethod' ] = $paymentMethod->get();

        return $this;
    }

    /**
     * Append the order's line item
     *
     * @param  array $lineItem
     * @return $this
     */
    public function lineItem(array $lineItem)
    {
        $this->attributes[ 'lineItems' ][] = $lineItem;

        return $this;
    }

    /**
     * Mutate the line items by attaching corresponding model
     *
     * This assumes offerId is set as per model ID
     *
     * @return array
     */
    public function getLineItems(){

        $lineItems = array_map(function($lineItem){
            return (new OrderLineItem)->with($lineItem)->all();
        }, $this->attributes['lineItems']);


        if(!($product_model = config('laravel-google-merchant-api.contents.products.model'))){
            return $lineItems;
        }

        return array_map(function($lineItem) use($product_model){
            $lineItem['model'] = $product_model::find($lineItem['product']['offerId']);

            return $lineItem;
        }, $lineItems);
    }


}
