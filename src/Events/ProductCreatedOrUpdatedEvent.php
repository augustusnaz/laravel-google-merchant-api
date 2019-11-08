<?php

namespace MOIREI\GoogleMerchantApi\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use MOIREI\GoogleMerchantApi\Contents\Product\Product;

class ProductCreatedOrUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;

    /**
     * Create a new event instance.
     *
     * @param Closure|array|Model $attributes
     * @return void
     */
    public function __construct($attributes)
    {
        if (is_callable($attributes)) {
            $callback = $attributes;

            $callback($product = new Product);
        }else{
            $product = (new Product)->with($attributes);
        }

        $this->product = $product;
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
