<?php

namespace MOIREI\GoogleMerchantApi\Listeners;

use MOIREI\GoogleMerchantApi\Facades\ProductApi;

class ProductDeletedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  any  $event
     * @return void
     */
    public function handle($event)
    {
        ProductApi::delete($event->product)->catch(function(){
            //
        });
    }
}