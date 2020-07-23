<?php

namespace MOIREI\GoogleMerchantApi\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use MOIREI\GoogleMerchantApi\Contents\Order\Order;

class NewOrdersScoutedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

	/**
	 * @var array
	 */
	public $orders;

	/**
	 * @var string
	 */
    public $merchant;

	/**
	 * @var string
	 */
    public $merchant_id;

    /**
     * Create a new event instance.
     *
     * @param array $orders
     * @param string $merchant
     * @param string $merchant_id
     * @return void
     */
    public function __construct(array $orders, $merchant = 'default', $merchant_id = null)
    {
        $this->orders = $orders;
        $this->merchant = $merchant;
        $this->merchant_id = $merchant_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [];
    }
}
