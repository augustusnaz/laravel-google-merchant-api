# Laravel Google API

A sweet package for managing Google Merchant Center feeds for Google Shopping. This package is prepared to implement the advanced [Content API](https://developers.google.com/shopping-content/v2.1/quickstart) for Merchants.



Example usage:

```php
use MOIREI\GoogleMerchantApi\Facades\ProductApi;
use MOIREI\GoogleMerchantApi\Facades\OrderApi;

...
    
ProductApi::insert(function($product){
    $product->offerId(1)
        	->title('Purple Shoes')
        	->description('What are thooose!!')
        	->price(10)
        	->custom('purchase_quantity_limit', 1000)
            ->availabilityDate( today()->addDays(7) );
})->then(function($response){
    echo 'Product inserted';
})->otherwise(function($response){
    echo 'Insert failed';
})->catch(function($e){
    echo($e->getResponse()->getBody()->getContents());
});

OrderApi::list()->then(function($response){
    //
})->otherwise(function($response){
    echo 'List failed';
})->catch(function($e){
    echo($e->getResponse()->getBody()->getContents());
});


OrderApi::scout(); // Scout and fire event
```



## Features

* **Products API**
  * Implements the `insert`, `get`, `delete` and `list` API calls
  * Uses defined schema interface for directly working with eloquent models (a product model)
  * Event listeners to respond to changes made to eloquent models, and `insert`  them automatically
* **Orders API**
  * Implements the `acknowledge`, `cancel`, `cancelLineItem`, `rejectReturnLineItem`, `returnRefundLineItem`, `get` and `list` API calls.
  * Internal schedule scouts un-acknowledged orders and fires an event. This means new orders on your Google Shopping can be automatically acknowledged and registered.
  * Includes sandbox functions for `testOrders`.




## Installation 

Via composer:

```bash
composer require moirei/laravel-google-merchant-api
```

Install the service provider (skip for Laravel>=5.5);

```
// config/app.php
'providers' => [
    ...
    MOIREI\GoogleMerchantApi\GoogleMerchantApiServiceProvider::class,
],
```

Publish the config

```php
php artisan vendor:publish --provider=MOIREI\GoogleMerchantApi\GoogleMerchantApiServiceProvider --tag="config"
```



## Setup & Authorisation

* Follow the instructions [here]( https://developers.google.com/shopping-content/v2/quickstart ) and create a service account key. Create `storage/app/google-merchant-api/service-account-credentials.json` in your app root and store the downloaded json contents
* Obtain your numeric Merchant ID
* Add your Merchant ID and the path to your service account credentials to the config
* In the config, setup the attributes section in product content if you need to use arrays or models



## Usage

### Product API

The Google Merchant contents can be queried via the `insert`, `get`, `delete`, and `list` methods. The product content is contained and handled via the `Product` class. An instance of this class can be passed directly or resolved in a Closure callback. An instance can be population by

* Directly accessing underlying attributes. See [special functions](doc/prodcut-conent-special-methods.md).
* Passing an eloquent model, or by
* Passing a raw array

To pass an array or a model, the attributes relationships must be defined in the config.

#### Insert

The insert method creates a new content, as well as updates an old content if the `channel`, `contentLanguage`, `targetCountry` and `offerId` are the same. 

```php
$attributes = [
    'id' => 1, // maps to offerId (if set in config)
    'name' => 'Product 1', // likewise maps to title
];
ProductApi::insert(function($product) use($attributes){
    $product->with($attributes)
        	->link('https://moirei.com/mg001')
        	->price(60, 'USD');
})->then(function($data){
    echo 'Product inserted';
})->otherwise(function(){
    echo 'Insert failed';
})->catch(function($e){
    dump($e);
});
```

**With arrays**:

```php
use MOIREI\GoogleMerchantApi\Contents\Product\Product as GMProduct;

...
$attributes = [
    'id' => 1,
    'name' => 'Product 1',
];
$product = (new GMProduct)->with($attributes);
```

The `attributes` values must be defined as per the attributes map in the config.

**With Eloquent Models**:

```php
use App\Models\Product;
use MOIREI\GoogleMerchantApi\Contents\Product\Product as GMProduct;


...
$model = Product::find(1);
$product = (new GMProduct)->with($model);

ProductApi::insert($product)->catch(function($e){
    // always catch exceptions
});
```

The model `attributes` values must be defined as per the attributes map in the config. For accessing undefined models attributes, use Accessors and custom Model attributes:

```php
protected $appends = [
    'availability',
    'gm_price',
];

...

public function getAvailabilityAttribute(){
    return 'in stock'; // calculate
}
public function getGmPriceAttribute(){
    return [
        'value' => $this->price,
        'currency' => $this->currency->code,
    ];
}
```

For setting custom Product contents (`customAttributes`), you're probably better off using the `custom()` method. Likewise for `availabilityDate` use the `availabilityUntil()` method.

**With Events & Listeners**:

The provided event and listener can be setup such that when your application creates or updates a model, the product content is automatically inserted. 

To set this up, add the following snippet to your eloquent mode.  The `product` variable can be a model or an array.

```php
use MOIREI\GoogleMerchantApi\Events\ProductCreatedOrUpdatedEvent;

...
    
/**
 * The "booting" method of the model.
 *
 * @return void
 */
protected static function boot() {
    parent::boot();

    // when a product is created
    static::created(function(Product $product){
        // perhaps a logic to ignore drafts and private products
        if($product->is_active && (config('app.env') === 'production')){
        	event(new ProductCreatedOrUpdatedEvent($product));   
        }
    });

    // when a product is updated
    static::updated(function(Product $product){
        // perhaps a logic to ignore drafts and private products
        if($product->is_active && (config('app.env') === 'production')){
        	event(new ProductCreatedOrUpdatedEvent(function($gm_product) use ($product){
                $gm_product->with($product)
                    	   ->preorder()
                    	   ->availabilityDate($product->preorder_date);
            }));   
        }
    });
}
```

Next, define the events relationship in `EventServiceProvider.php`.

```php
use MOIREI\GoogleMerchantApi\Listeners\ProductCreatedOrUpdatedListener;

...
    
/**
 * The event listener mappings for the application.
 *
 * @var array
 */
protected $listen = [
    ...,
    /**
     * Product events
     */
    ProductCreatedOrUpdatedEvent::class => [
        ProductCreatedOrUpdatedListener::class,
    ],

];
```

#### Get & List

```php
ProductApi::get($product)->then(function($data){
    //
})->catch(function($e){
    // always catch exceptions
});
```

The `list` method calls the `get` method without any parameters;

```php
ProductApi::list()->then(function($data){
    //
});
```

So the following should likewise retrieve the product list:

```php
ProductApi::get()->then(function($data){
    //
});
```

#### Delete

```php
ProductApi::delete($product)->then(function($data){
    //
});
```

To set up with the event listener, add the following to your eloquent model:

```php
use MOIREI\GoogleMerchantApi\Events\ProductDeletedEvent;

...
    
protected static function boot() {
    parent::boot();

    ...
        
    // when a product is deleted
    static::deleted(function(Product $product){
        if(config('app.env') === 'production'){
        	event(new ProductDeletedEvent($product)); 
        }
    });
}
```

Then define the relationship in `EventServiceProvider.php`:

```php
use MOIREI\GoogleMerchantApi\Listeners\ProductDeletedListener;

...
    
protected $listen = [
    ...,
    ProductDeletedEvent::class => [
        ProductDeletedListener::class,
    ],
];
```



# Order API

***Please note that these implementations have not been properly tested.***

#### Using the API methods

The `acknowledge`, `cancel`, `cancelLineItem`, `rejectReturnLineItem`, `returnRefundLineItem`, `get`, `list` methods are currently implemented for interacting with your Google Merchant.

The format for using these methods are standard across the entire package. For example, an order can be acknowledged by

```php
OrderApi::acknowledge(function($order){
    $order->id('TEST-1953-43-0514');
});
```

or by

```php
$order = (new Order)->with([
    'id' => 'TEST-1953-43-0514',
]);
OrderApi::acknowledge($order);
```

Additionally the `listAcknowledged` method is provided so one can list acknowledged orders if needed.

#### Scheduled Scouts

If `schedule_orders_check` is set as true in the config, the package will regularly scout un-acknowledged orders and will fire a `\MOIREI\GoogleMerchantApi\Events\NewOrdersScoutedEvent` event. This event includes an **array** of orders of class `\MOIREI\GoogleMerchantApi\Contents\Order`. The orders are structured as per the [Order Resource](https://developers.google.com/shopping-content/v2/reference/v2.1/orders#resource).

Example handle in your listener:

```php
use MOIREI\GoogleMerchantApi\Events\NewOrdersScoutedEvent;
use MOIREI\GoogleMerchantApi\Facades\OrderApi;

...
public function handle(NewOrdersScoutedEvent $event)
{
    foreach($event->orders as $gm_order){
        OrderApi::acknowledge($gm_order);

        $gm_order = $gm_order->all(); // get all attributes, including mutated attributes
        foreach($gm_order['lineItems'] as $line_item){
            $model = $line_item['model']; // retrieves model
            $quantity = $line_item['quantityOrdered'];
            $shipping = $line_item['shippingDetails'];
            $delivery_date = $shipping['deliverByDate']->diffForHumans();
            
            // register new order item
        }
		
        // register new order
    }
}
```

**Notes**: 

* Accessing the `lineItems` will automatically resolve and attach the corresponding model to each item. Of course this assumes your inserted products' `offerId` correspond to the model's ID & primary key.
* If you haven't already started Laravel scheduler, you'll need to add the following Cron entry to your server. `* * * * * php artisan schedule:run >> /dev/null 2>&1`.
* It's important you test that the scheduler is set up correctly. For this reason, the `MOIREI\GoogleMerchantApi\Events\OrderContentScoutedEvent` event is provided. If `debug_scout` is set to true in the config, this event is fired whenever the scheduler fires.

#### Sandboxing

The OrderApi class provide a way of calling some of the sandbox operations. Example:

```php
OrderApi::sandbox()->create(function($order){
    $order->shippingCost(30)
          ->shippingOption('economy')
          ->predefinedEmail('pog.dwight.schrute@gmail.com') 
          ->predefinedDeliveryAddress('dwight');
})
```

You may use

```php
OrderApi::sandbox()->testCreate();
```

to use a preset example.

Implemented sandbox actions: 

| Function       | Sandbox Action   |
| -------------- | ---------------- |
| `create`       | createtestorder  |
| `advance`      | advancetestorder |
| `cancel`       | createtestorder  |
| `createReturn` | createtestreturn |



## Commands

This package provides an artisan command for scouting orders.

```bash
php artisan gm-orders:scout
```



## Handling Errors

Methods that throw exceptions

* `MOIREI\GoogleMerchantApi\Contents\Product::with()`

  throws `MOIREI\GoogleMerchantApi\Exceptions\ProductContentAttributesUndefined` if the supplied attributes is not a Model or array.

* The `insert`, `get`, `delete`, `list`, `listAcknowledged` and `scout` methods in the API classes will throw `GuzzleHttp\Exception\ClientException` if the client request is corrupted, fails, not defined or not authorised. 

* The `MOIREI\GoogleMerchantApi\Exceptions\Invalid**Input` exceptions are thrown if an unresolvable entity is passed as a content attribute.

Exceptions should be handled using the `catch` function. If making synchronous calls, use the try-catch block. You'd be well advised to always catch requests (and notify your business logic), seeing that Google has a million reasons to deny any request.



## Design Notes

* Insert, List, Get, Delete methods will always return a clone of the original instance if using the default asynchronous feature. This allows the then, otherwise, and catch callbacks of multiple requests to not override. These methods return a Guzzle response if set to synchronous mode.
* If the delete method is called and the resolved content ID is invalid, it returns without making any requests or throwing any errors. If the get method, it returns a list of products or orders. 
* A valid product content ID follows the pattern *online:en:AU:1* i.e. `channel:contentLanguage:targetCountry:offerId`. This ID is of course auto generated; and the attributes, except for `offerId`, have default values.
* Requests can take up to 2 hours before they reflect on your Google Merchant Center. Patience!
* Unlike the ProductApi or OrderApi classes, the events constructor may take a Model, array or callback.
* Calling the `all` method on a `Product`, `Order` or any content class will resolve all mutated attributes. e.g. `$order['lineItems'][0]['shippingDetails']['deliverByDate']` returns a `Carbon`.



#### Synchronous Calls

All the above are by default asynchronous. To make synchronous calls, use the `sync` method:

```php
try{
    $response = ProductApi::sync()->insert(function($product){
        $product->offerId(1)
            	->country('AU')
            	->inStock(false);
    });
}catch(\GuzzleHttp\Exception\ClientException $e){
    //
}
```

**Note**: In this case, methods such as `insert`, `get`, `delete`, and `list`, etc, returns a Guzzle response when called asynchronously (rather than an instance of `ProductApi` or `OrderApi`. This means your exception blocks should be wrapped around requests.



## Contributing

This package is intended to provide a Laravel solution for the Google Shopping API for Google Merchant. Currently, only the Product Content has been adequately implemented and tested. For orders, refunds, etc., ideas and pull-requests are welcome.




## Credits

- [Augustus Okoye](https://github.com/augustusnaz)



## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.