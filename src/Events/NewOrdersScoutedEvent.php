<?php

namespace MOIREI\GoogleMerchantApi\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use MOIREI\GoogleMerchantApi\Contents\Order\Order;

class NewOrdersScoutedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orders;

    /**
     * Create a new event instance.
     *
     * @param array $orders
     * @return void
     */
    public function __construct(array $orders)
    {
        $this->orders = $orders;
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
