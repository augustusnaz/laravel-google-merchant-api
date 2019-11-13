<?php

namespace MOIREI\GoogleMerchantApi\Api;

use MOIREI\GoogleMerchantApi\Contents\Order\Order;
use MOIREI\GoogleMerchantApi\Exceptions\InvalidOrderInput;
use MOIREI\GoogleMerchantApi\Events\NewOrdersScoutedEvent;
use MOIREI\GoogleMerchantApi\Events\OrderContentScoutedEvent;
use MOIREI\GoogleMerchantApi\Contents\Price;

class OrderApi extends AbstractApi{

    /**
     * Allowed types of "reasons"
     *
     * @var array $allowed_reasons
     */
    public static $allowed_reasons = [
        'customerInitiatedCancel', 'invalidCoupon', 'malformedShippingAddress',
        'noInventory', 'other', 'priceError', 'shippingPriceError', 'taxError',
        'undeliverableShippingAddress', 'unsupportedPoBoxAddress',
    ];

	/**
	 * Setup the resource api
	 *
	 * @param string $endpoint
	 * @param string $mode 'production', 'sandbox'
	 */
	public function __construct($endpoint = 'orders', $mode = 'production') {
		parent::__construct( $endpoint, $mode );
    }

    /**
     * Make a new instance of the test class
     */
    static public function sandbox(){
        return new OrderApiSandbox();
    }

    /**
     * Acknowledge an order.
     *
     * @param  Closure|Order  $order
     * @return mix
	 * @throws \GuzzleHttp\Exception\ClientException
     * @throws MOIREI\GoogleMerchantApi\Exceptions\InvalidOrderInput
     */
    public function acknowledge($order)
    {
        $order = self::resolveOrderInput($order);
        $instance = self::getInstance($this);

        $instance->setRequestArgs([
			'path' => $instance->getId($order) . '/acknowledge',
        ]);

        return $instance->post([
            'operationId' => $order->operationId,
        ]);
    }

    /**
     * Advance test order.
     *
     * @param  Closure|Order  $order
     * @param  string $reason
     * @param  string $reason_text
     * @return mix
	 * @throws \GuzzleHttp\Exception\ClientException
     * @throws MOIREI\GoogleMerchantApi\Exceptions\InvalidOrderInput
     */
    public function cancel($order, string $reason = 'other', string $reason_text = 'Other')
    {
        $order = self::resolveOrderInput($order);
        $instance = self::getInstance($this);

        $instance->setRequestArgs([
			'path' => $instance->getId($order) . '/cancel',
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
     * Cancel order line item.
     *
     * @param  Closure|Order  $order
     * @param  string $lineItemId
     * @param  string $productId
     * @param  integer $quantity
     * @param  string $reason
     * @param  string $reason_text
     * @return mix
	 * @throws \GuzzleHttp\Exception\ClientException
     * @throws MOIREI\GoogleMerchantApi\Exceptions\InvalidOrderInput
     */
    public function cancelLineItem($order, string $lineItemId, string $productId, $quantity = 1, string $reason = 'other', string $reason_text = 'Other')
    {
        $order = self::resolveOrderInput($order);
        $instance = self::getInstance($this);

        $instance->setRequestArgs([
			'path' => $instance->getId($order) . '/cancelLineItem',
        ]);

        if(!in_array($reason, self::$allowed_reasons)){
            $reason = 'other';
        }

        return $instance->post([
            'operationId' => $order->operationId,
            'lineItemId' => $lineItemId,
            'productId' => $productId,
            'quantity' => $quantity, // unsigned integer
            'reason' => $reason, // string
            'reasonText' => $reason_text, // string
        ]);
    }

    /**
     * Reject return on an line item.
     *
     * @param  Closure|Order  $order
     * @param  string $lineItemId
     * @param  string $productId
     * @param  integer $quantity
     * @param  string $reason
     * @param  string $reason_text
     * @return mix
	 * @throws \GuzzleHttp\Exception\ClientException
     * @throws MOIREI\GoogleMerchantApi\Exceptions\InvalidOrderInput
     */
    public function rejectReturnLineItem($order, string $lineItemId, string $productId, $quantity = 1, string $reason = 'other', string $reason_text = 'Other')
    {
        $order = self::resolveOrderInput($order);
        $instance = self::getInstance($this);

        $instance->setRequestArgs([
			'path' => $instance->getId($order) . '/rejectReturnLineItem',
        ]);

        if(!in_array($reason, self::$allowed_reasons)){
            $reason = 'other';
        }

        return $instance->post([
            'operationId' => $order->operationId,
            'lineItemId' => $lineItemId,
            'productId' => $productId,
            'quantity' => $quantity, // unsigned integer
            'reason' => $reason, // string
            'reasonText' => $reason_text, // string
        ]);
    }

    /**
     * Reject return on an line item.
     *
     * @param  Closure|Order  $order
     * @param  string $lineItemId
     * @param  string $productId
     * @param  Closure|Price $priceAmount
     * @param  Closure|Price $taxAmount
     * @param  integer $quantity
     * @param  string $reason
     * @param  string $reason_text
     * @return mix
	 * @throws \GuzzleHttp\Exception\ClientException
     * @throws MOIREI\GoogleMerchantApi\Exceptions\InvalidOrderInput
     */
    public function returnRefundLineItem($order, string $lineItemId, string $productId, $priceAmount, $taxAmount, $quantity = 1, string $reason = 'other', string $reason_text = 'Other')
    {
        $order = self::resolveOrderInput($order);
        $instance = self::getInstance($this);

        $instance->setRequestArgs([
			'path' => $instance->getId($order) . '/returnRefundLineItem',
        ]);

        if(!in_array($reason, self::$allowed_reasons)){
            $reason = 'other';
        }

        if(is_array($priceAmount)){
            $priceAmount = (new Price)->with($priceAmount);
        }elseif (is_callable($priceAmount)) {
            $callback = $priceAmount;
            $callback($priceAmount = new Price);
        }elseif(!($priceAmount instanceof Price)){
            throw new \MOIREI\GoogleMerchantApi\Exceptions\InvalidPriceInput;
        }

        if(is_array($taxAmount)){
            $taxAmount = (new Price)->with($taxAmount);
        }elseif (is_callable($taxAmount)) {
            $callback = $taxAmount;
            $callback($taxAmount = new Price);
        }elseif(!($taxAmount instanceof Price)){
            throw new \MOIREI\GoogleMerchantApi\Exceptions\InvalidPriceInput;
        }

        return $instance->post([
            'operationId' => $order->operationId,
            'lineItemId' => $lineItemId,
            'productId' => $productId,
            'quantity' => $quantity, // unsigned integer
            'reason' => $reason, // string
            'reasonText' => $reason_text, // string
            'priceAmount' => $priceAmount->get(),
            'taxAmount' => $taxAmount->get(),
        ]);
    }

    /**
     * Get order by order.
     *
     * @param Order|null|Closure $order
     * @return mix
	 * @throws \GuzzleHttp\Exception\ClientException
     * @throws MOIREI\GoogleMerchantApi\Exceptions\InvalidOrderInput
     */
    public function get($order = null, $params = array()){
        $instance = self::getInstance($this);

        if(is_null($order)){
            $id = null;
        }else{
            $id = $instance->getId( self::resolveOrderInput($order) );
        }

        $instance->setRequestArgs( array(
			'method' => 'GET',
			'path'   => $id,
		) );

		$instance->clearCallbacks();

		return $instance->execRequest();
    }

    /**
     * List un-acknowledged orders.
     *
     * @return mix
	 * @throws \GuzzleHttp\Exception\ClientException
     */
    public function list(){
        return $this->get();
    }

    /**
     * List acknowledged orders.
     *
     * @return mix
	 * @throws \GuzzleHttp\Exception\ClientException
     */
    public function listAcknowledged(){
        $instance = self::getInstance($this);

        $instance->setRequestArgs( array(
			'method' => 'GET',
			'path'   => '?acknowledged=true',
		) );

		$instance->clearCallbacks();

		return $instance->execRequest();
    }

    /**
     * Scout Google Merchant for un-acknowledged orders and take actions
     *
	 * @throws \GuzzleHttp\Exception\ClientException
     */
    public function scout(){

        $response = $this->sync()->list();
        if($response->getStatusCode() === 200){
            $data = json_decode($response->getBody(), true);
            if(count($resource)){
                $orders = array_map(function($resource){
                    return (new Order)->with($resource);
                }, $data->resources);
                event(new NewOrdersScoutedEvent($orders));
            }
        }else{
            //
        }

        if(config('laravel-google-merchant-api.contents.orders.debug_scout', false)){
            event(new OrderContentScoutedEvent());
        }
    }

    /**
     * Get order id
     *
     * @param Order|null $order
     * @return string|null
     */
    protected function getId($order){
        if(is_null($order)) return null;

        return $order->id;
    }

    /**
     * Get OrderApi instance
     *
     * @param OrderApi $orderApi
     * @return OrderApi
     */
    static protected function getInstance(OrderApi $orderApi){
        if($orderApi->async){
            // duplicate so that callbacks are not overridden
            return clone $orderApi;
        }else{
            return $orderApi;
        }
    }

    /**
     * Resolve order input
     *
     * @param Order|Closure $order
     * @return Order
     * @throws MOIREI\GoogleMerchantApi\Exceptions\InvalidOrderInput
     */
    static protected function resolveOrderInput($order){
        if (is_callable($order)) {
            $callback = $order;

            $callback($order = new Order);
        }

        if( !($order instanceof Order) ){
            throw new InvalidOrderInput;
        }

        return $order;
    }
}