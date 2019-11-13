<?php

namespace MOIREI\GoogleMerchantApi\Api;

use Closure;
use MOIREI\GoogleMerchantApi\Contents\Product\Product;
use MOIREI\GoogleMerchantApi\Exceptions\InvalidProductInput;

class ProductApi extends AbstractApi{

	/**
	 * Setup the resource api
	 *
	 */
	public function __construct() {

		parent::__construct( 'products' );
    }

    /**
     * Insert product(s).
     *
     * @param  Closure|Product  $product
     * @return mix
	 * @throws \GuzzleHttp\Exception\ClientException
     * @throws MOIREI\GoogleMerchantApi\Exceptions\InvalidProductInput
     */
    public function insert($product)
    {
        $instance = self::getInstance($this);
        $product = self::resolveProductInput($product);

        return $instance->post($product->get());
    }

    /**
     * List products.
     *
     * @return mix
	 * @throws \GuzzleHttp\Exception\ClientException
     * @throws MOIREI\GoogleMerchantApi\Exceptions\InvalidProductInput
     */
    public function list(){
        return $this->get();
    }

    /**
     * Get product by product.
     *
     * @param Product|null|Closure $product
     * @return mix
	 * @throws \GuzzleHttp\Exception\ClientException
     * @throws MOIREI\GoogleMerchantApi\Exceptions\InvalidProductInput
     */
    public function get($product = null, $params = array()){
        $instance = self::getInstance($this);

        if(is_null($product)){
            $id = null;
        }else{
            $id = $instance->getId( self::resolveProductInput($product) );
        }

        $instance->setRequestArgs( array(
			'method' => 'GET',
			'path'   => $id,
		) );

		$instance->clearCallbacks();

		return $instance->execRequest();
    }

    /**
     * Delete product(s).
     *
     * @param Product|Closure $product
     * @return mix
	 * @throws \GuzzleHttp\Exception\ClientException
     * @throws MOIREI\GoogleMerchantApi\Exceptions\InvalidProductInput
     */
	public function delete($product) {
        $instance = self::getInstance($this);
        $product = self::resolveProductInput($product);

        if(is_null($id = $instance->getId($product))){
            return $instance;
        }

        $instance->setRequestArgs( array(
			'method' => 'DELETE',
			'path'   => $id,
		) );

		$instance->clearCallbacks();

		return $instance->execRequest();
    }

    /**
     * Get product id in format online:en:US:1111111111
     *
     * @param Product|null $product
     * @return string|null
     */
    private function getId($product){

        if(is_null($product) || !($product instanceof Product)) return null;

        $channel = $product->dataGet('channel', config('laravel-google-merchant-api.contents.products.defaults.channel', 'online'));
        $contentLanguage = $product->dataGet('contentLanguage', config('laravel-google-merchant-api.contents.products.defaults.contentLanguage', 'en'));
        $targetCountry = $product->dataGet('targetCountry', config('laravel-google-merchant-api.contents.products.defaults.targetCountry', 'AU'));
        $offerId = $product->dataGet('offerId');

        if($channel && $contentLanguage && $targetCountry && $offerId){
            return "$channel:$contentLanguage:$targetCountry:$offerId";
        }

        return null;
    }

    /**
     * Get ProductApi instance
     *
     * @param ProductApi $productApi
     * @return ProductApi
     */
    static protected function getInstance(ProductApi $productApi){
        if($productApi->async){
            // duplicate so that callbacks are not overridden
            return clone $productApi;
        }else{
            return $productApi;
        }
    }

    /**
     * Resolve product input
     *
     * @param Product|Closure $product
     * @return Product
     * @throws MOIREI\GoogleMerchantApi\Exceptions\InvalidProductInput
     */
    static protected function resolveProductInput($product){
        if (is_callable($product)) {
            $callback = $product;

            $callback($product = new Product);
        }

        if( !($product instanceof Product) ){
            throw new InvalidProductInput;
        }

        return $product;
    }
}