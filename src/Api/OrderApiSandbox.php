<?php

namespace MOIREI\GoogleMerchantApi\Api;

use MOIREI\GoogleMerchantApi\Contents\Order\Order;
use MOIREI\GoogleMerchantApi\Contents\Order\OrderTest;
use MOIREI\GoogleMerchantApi\Contents\Product\Product;

class OrderApiSandbox extends OrderApi{

	/**
	 * Setup the resource api
	 *
	 */
	public function __construct() {
		parent::__construct( 'testorders', 'sandbox' );
	}

	public function testCreate(){

        $order = new OrderTest;

        $order->kind('content#testOrder')
            ->shippingCost(30)
            // ->shippingCostTax(10)
            // ->paymentMethod(function($method){
            //     $method->type('Visa')
            //            ->lastFourDigits('5555')
            //            ->predefinedBillingAddress('7 James Ave.')
            //            ->expirationMonth(5)
            //            ->expirationYear(2020);
            // })
            ->shippingOption('economy') // Allowed values: 'economy', 'expedited', 'oneDay', 'sameDay', 'standard', 'twoDay'
            ->predefinedEmail('pog.dwight.schrute@gmail.com') // Allowed values: 'pog.dwight.schrute@gmail.com', 'pog.jim.halpert@gmail.com', 'pog.pam.beesly@gmail.com'
            ->predefinedDeliveryAddress('dwight') // Allowed values: 'dwight, 'jim', 'pam'
            ->predefinedBillingAddress('dwight') // Allowed values: 'dwight, 'jim', 'pam'
            ->lineItem([
                'product' => (new Product)
                             ->kind(null)->channel(null)->availability(null) // unset
                             ->title('Wireless Power Bank')
                             ->brand('MOIREI')
                             ->condition('new')
                             ->contentLanguage('en')
                             ->targetCountry('US')
                             ->imageLink('https://mrsc.moirei.com/storage/media/new-moirei-qi-wireless-power-bank-10000-mah-fast-charge-type-c-usb-qc-wireless-pd-charging-mobile-po-1571021317-PMNsS.jpg')
                             ->offerId(5)
                             ->price(59, 'USD')
                             ->get(),
                'quantityOrdered' => 2,
                'returnInfo' => [
                    'isReturnable' => true,
                    'daysToReturn' => 15,
                    'policyUrl' => 'https://www.moirei.com/shop/returns',
                ],
                'shippingDetails' => [
                    'deliverByDate' => '2019-11-20T12:34:02',
                    'method' => [
                        'methodName' => 'Post',
                        'carrier' => 'Post',
                        'minDaysInTransit' => 2,
                        'maxDaysInTransit' => 5,
                    ],
                    'shipByDate' => '2019-11-20T12:34:02'
                ],
            ]);

        return $this->create( $order );
	}

    /**
     * Create test order(s).
     *
     * @param  Closure|order  $order callback, TestOrder or the ID
     * @param  string  $country
     * @return mix
	 * @throws \GuzzleHttp\Exception\ClientException
     * @throws MOIREI\GoogleMerchantApi\Exceptions\InvalidOrderInput
     */
    public function create($order, $country = 'US')
    {
        $order = self::resolveOrderInput($order);
        $instance = self::getInstance($this);

        return $instance->post([
            'country' => $country, // Allowed values: 'US', 'FR'
            'testOrder' => $order->get(),
            // 'templateName' => '',
        ]);
    }

    /**
     * Advance test order.
     *
     * @param  Closure|OrderTest|string  $order callback, TestOrder or the ID
     * @return mix
	 * @throws \GuzzleHttp\Exception\ClientException
     * @throws MOIREI\GoogleMerchantApi\Exceptions\InvalidOrderInput
     */
    public function advance($order)
    {
        $instance = self::getInstance($this);
        if(is_string($order)){
            $order_id = $order;
        }else{
            $order_id = $instance->getId( self::resolveOrderInput($order) );
        }

        $instance->setRequestArgs([
			'path' => $order_id . '/advance',
        ]);

        return $instance->post();
    }

    /**
     * Advance test order by customer.
     *
     * @param  Closure|OrderTest  $order
     * @param  string $reason
     * @param  string $reason_text
     * @return mix
	 * @throws \GuzzleHttp\Exception\ClientException
     * @throws MOIREI\GoogleMerchantApi\Exceptions\InvalidOrderInput
     */
    public function cancel($order, string $reason = 'other', string $reason_text = 'Order cancel test')
    {
        $instance = self::getInstance($this);
        if(is_string($order)){
            $order_id = $order;
        }else{
            $order_id = $instance->getId( self::resolveOrderInput($order) );
        }

        $instance->setRequestArgs([
			'path' => $order_id . '/cancelByCustomer',
        ]);

        if(!in_array($reason, self::$allowed_reasons)){
            $reason = 'other';
        }

        return $instance->post([
            'operationId' => $order->operationId,
            'reason' => $reason,
            'reasonText' => $reason_text,
        ]);
    }

    /**
     * Create test order return.
     *
     * @param  Closure|OrderTest|string  $order callback, TestOrder or the ID
     * @param  Closure|array  $items
     * @return mix
	 * @throws \GuzzleHttp\Exception\ClientException
     * @throws MOIREI\GoogleMerchantApi\Exceptions\InvalidOrderInput
     */
    public function createReturn($order, $items)
    {
        $instance = self::getInstance($this);
        if(is_string($order)){
            $order_id = $order;
        }else{
            $order_id = $instance->getId( self::resolveOrderInput($order) );
        }

        $instance->setRequestArgs([
			'path' => $order_id . '/testreturn',
        ]);

		/**
		 *
		 * items structure:
		 *
		 * "lineItemId": string,
		 * "quantity": unsigned integer
		 */
        return $instance->post([
            'items' => $items,
        ]);
    }

    /**
     * Resolve order input
     *
     * @param OrderTest|Closure $order
     * @return OrderTest
     * @throws MOIREI\GoogleMerchantApi\Exceptions\InvalidOrderTestInput
     */
    static protected function resolveOrderTestInput($order){
        if (is_callable($order)) {
            $callback = $order;

            $callback($order = new OrderTest);
        }

        if( !($order instanceof OrderTest) ){
            throw new InvalidOrderInput;
        }

        return $order;
    }

}