<?php

namespace MOIREI\GoogleMerchantApi\Contents\Product;

use MOIREI\GoogleMerchantApi\Contents\BaseContent;
use Closure;
use Carbon\Carbon;
use MOIREI\GoogleMerchantApi\Contents\Price;
use MOIREI\GoogleMerchantApi\Contents\Measure;
use MOIREI\GoogleMerchantApi\Contents\Taxes;

class Product extends BaseContent
{
    /**
     * Allowed attributes.
     *
     * @var string  kind
     * @var string  channel
     * @var string  contentLanguage
     * @var integer offerId
     * @var string  targetCountry
     * @var string  title
     * @var string  description
     * @var string  link
     * @var string  imageLink
     * @var array   additionalImageLinks
     * @var string  adsGrouping
     * @var array   adsLabels
     * @var string  adsRedirect
     * @var boolean adult
     * @var string  ageGroup
     * @var string  availability
     * @var string  availabilityDate
     * @var string  brand
     * @var string  color
     * @var string  condition
     * @var array   costOfGoodsSold
     * @var string  gender
     * @var string  googleProductCategory
     * @var string  gtin
     * @var string  itemGroupId
     * @var string  mpn
     * @var array   price
     * @var array   salePrice
     * @var string  salePriceEffectiveDate
     * @var long    sellOnGoogleQuantity
     * @var array   shipping
     * @var array   sizes
     * @var string  sizeSystem
     * @var string  sizeType
     * @var array   customAttributes
     * @var string  displayAdsId
     * @var string  displayAdsLink
     * @var array   displayAdsSimilarIds
     * @var string  displayAdsTitle
     * @var double  displayAdsValue
     * @var string  energyEfficiencyClass
     * @var array   excludedDestinations
     * @var string  expirationDate
     * @var boolean identifierExists
     * @var array   includedDestinations
     * @var array   installment
     * @var boolean isBundle
     * @var array   loyaltyPoints
     * @var string  material
     * @var string  maxEnergyEfficiencyClass
     * @var long    maxHandlingTime
     * @var string  minEnergyEfficiencyClass
     * @var long    minHandlingTime
     * @var string  mobileLink
     * @var long    multipack
     * @var string  pattern
     * @var array   shippingHeight
     * @var string  shippingLabel
     * @var array   shippingLength
     * @var string  shippingWeight
     * @var string  taxCategory
     * @var array   taxes
     * @var string  transitTimeLabel
     * @var array   unitPricingBaseMeasure
     * @var array   unitPricingMeasure
     *
     * @var array
     */
    protected $allowed_attributes = [
        'kind',
        'channel', 'contentLanguage', 'offerId', 'targetCountry',
        'title', 'description', 'link', 'imageLink', 'additionalImageLinks',
        'adsGrouping', 'adsLabels', 'adsRedirect', 'adult', 'ageGroup',
        'availability', 'availabilityDate', 'brand', 'color', 'condition', 'costOfGoodsSold',
        'gender', 'googleProductCategory', 'gtin', 'itemGroupId', 'mpn',
        'price', 'salePrice', 'salePriceEffectiveDate', 'sellOnGoogleQuantity', 'shipping', 'sizes', 'customAttributes',
        'customLabel0', 'customLabel1', 'customLabel2', 'customLabel3', 'customLabel4',
        'displayAdsId', 'displayAdsLink', 'displayAdsSimilarIds', 'displayAdsTitle', 'displayAdsValue',
        'energyEfficiencyClass', 'excludedDestinations', 'expirationDate',
        'identifierExists', 'includedDestinations', 'installment', 'isBundle',
        'loyaltyPoints', 'material', 'maxEnergyEfficiencyClass', 'maxHandlingTime', 'minEnergyEfficiencyClass', 'minHandlingTime',
        'mobileLink', 'multipack', 'pattern',
        'shippingHeight', 'shippingLabel', 'shippingLength', 'shippingWeight',
        'sizeSystem', 'sizeType', 'taxCategory', 'taxes', 'transitTimeLabel', 'unitPricingBaseMeasure', 'unitPricingMeasure',
    ];


	/**
	 * Setup the product content
	 */
    public function __construct(){

        $this->attributes['kind'] = 'content#product';
        $this->attributes['sizes'] = array();
        $this->attributes['customAttributes'] = array();
        $this->attributes['shipping'] = array();
        $this->attributes['taxes'] = array();

        // Set defaults
        foreach(config('laravel-google-merchant-api.contents.products.defaults', []) as $attribute => $default){
            if(in_array($attribute, $this->allowed_attributes)){
                $this->attributes[ $attribute ] = $default;
            }
        }

    }

    public function __call($name, $arguments) {
        if(in_array($name, $this->allowed_attributes)){
            $this->attributes[ $name ] = $arguments[0];
        }else{
            throw new \BadMethodCallException("Instance method Product->$name() doesn't exist");
        }

        return $this;
    }

    public function __get($attribute){
        if(isset($this->attributes[ $attribute ]) && !empty($this->attributes[ $attribute ])){
            return $this->attributes[ $attribute ];
        }

        return null;
    }


    /**
     * Batch fill with array
     *
     * @param array|Model $attributes
     * @throws MOIREI\GoogleMerchantApi\Exceptions\ProductContentAttributesUndefined
     */
    public function with($attributes){

        if( !($attributes_map = config('laravel-google-merchant-api.contents.products.attributes')) ){
            throw new \MOIREI\GoogleMerchantApi\Exceptions\ProductContentAttributesUndefined;
        }

        if($attributes instanceof \Illuminate\Database\Eloquent\Model){
            $attributes = $attributes->toArray();
        }

        $attributes_map = collect($attributes_map)->only($this->allowed_attributes)->all();

        $attributes = collect($attributes)->only(array_values($attributes_map))->all();

        foreach($attributes_map as $key => $attribute){
            if(isset($attributes[ $attribute ])){
                $this->attributes[ $key ] = $attributes[ $attribute ];
            }
        }

        return $this;
    }

    /**
     * Set the product image link.
     *
     * @param  string $imageLink
     * @return $this
     */
    public function image(string $imageLink)
    {
        $this->attributes[ 'imageLink' ] = $imageLink;

        return $this;
    }

    /**
     * Set the product content language.
     *
     * @param  string $contentLanguage
     * @return $this
     */
    public function lang(string $contentLanguage)
    {
        $this->attributes[ 'contentLanguage' ] = $contentLanguage;

        return $this;
    }

    /**
     * Set the product target country.
     *
     * @param  string $targetCountry
     * @return $this
     */
    public function country(string $targetCountry)
    {
        $this->attributes[ 'targetCountry' ] = $targetCountry;

        return $this;
    }

    /**
     * Set the product as online.
     *
     * @param  boolean $online
     * @return $this
     */
    public function online($online = true)
    {
        if($online){
            $this->attributes[ 'channel' ] = 'online';
        }else{
            $this->attributes[ 'channel' ] = 'local';
        }

        return $this;
    }

    /**
     * Set the product availability.
     *
     * @param  boolean  $inStock
     * @return $this
     */
    public function inStock($inStock = true)
    {
        if($inStock){
            $this->attributes[ 'availability' ] = 'in stock';
        }
        else{
            $this->attributes[ 'availability' ] = 'out of stock';
        }

        return $this;
    }

    /**
     * Set the product availability.
     *
     * @return $this
     */
    public function preorder()
    {
        $this->attributes[ 'availability' ] = 'preorder';

        return $this;
    }

    /**
     * Set the product stock availability date.
     *
     * @param  Carbon\Carbon|string $until
     * @return $this
     */
    public function availabilityDate($until)
    {
        if( !($until instanceof Carbon) ){
            $until = new Carbon($until);
        }

        $this->attributes[ 'availabilityDate' ] = $until->format('Y-m-d') . 'T' . $until->format('h:i:s');

        return $this;
    }

    /**
     * Set the product stock availability date.
     *
     * Date on which the product should expire, in ISO 8601 format.
     * Google: "The actual expiration date in Google Shopping is exposed in productstatuses as googleExpirationDate and might be earlier if expirationDate is too far in the future"
     *
     * @param  Carbon\Carbon|string $until
     * @return $this
     */
    public function expirationDate($date)
    {
        if( !($date instanceof Carbon) ){
            $date = new Carbon($date);
        }

        $this->attributes[ 'expirationDate' ] = $date->format('Y-m-d') . 'T' . $date->format('h:i:s');

        return $this;
    }

    /**
     * Set the product's google product category.
     *
     * @param  string $googleProductCategory
     * @return $this
     */
    public function category(string $googleProductCategory)
    {
        $this->attributes[ 'googleProductCategory' ] = $googleProductCategory;

        return $this;
    }

    /**
     * Set the product's price.
     *
     * @param  Closure|string|float|array $price
     * @param  string|null $currency
     * @return $this
     */
    public function price($price, $currency = null)
    {
        if(is_numeric($price) || is_string($price)){
            if(is_null($currency)){
                $currency = config('laravel-google-merchant-api.default_currency', 'AUD');
            }
            $price = (new Price)->value($price)->currency($currency);
        }elseif(is_array($price)){
            $price = (new Price)->with($price);
        }elseif (is_callable($price)) {
            $callback = $price;
            $callback($price = new Price);
        }elseif(!($price instanceof Price)){
            throw new \MOIREI\GoogleMerchantApi\Exceptions\InvalidPriceInput;
        }

        $this->attributes[ 'price' ] = $price->get();

        return $this;
    }

    /**
     * Set the product's sale price.
     *
     * @param  Closure|string|float|array $price
     * @param  string|null $currency
     * @return $this
     */
    public function salePrice($price, $currency = null)
    {
        if(is_numeric($price) || is_string($price)){
            if(is_null($currency)){
                $currency = config('laravel-google-merchant-api.default_currency', 'AUD');
            }
            $price = (new Price)->value($price)->currency($currency);
        }elseif(is_array($price)){
            $price = (new Price)->with($price);
        }elseif (is_callable($price)) {
            $callback = $price;
            $callback($price = new Price);
        }elseif(!($price instanceof Price)){
            throw new \MOIREI\GoogleMerchantApi\Exceptions\InvalidPriceInput;
        }

        $this->attributes[ 'salePrice' ] = $price->get();

        return $this;
    }

    /**
     * Set the product's sale price effective date.
     *
     * Date range during which the item is on sale.
     *
     * @param  Closure|ProductShipping|array $shipping
     * @return $this
     */
    public function shipping($shipping)
    {
        if(is_array($shipping)){
            $shipping = (new ProductShipping)->with($shipping);
        }elseif (is_callable($shipping)) {
            $callback = $shipping;
            $callback($shipping = new ProductShipping);
        }elseif(!($shipping instanceof ProductShipping)){
            throw new \MOIREI\GoogleMerchantApi\Exceptions\InvalidProductShippingInput;
        }

        $this->attributes[ 'shipping' ][] = $shipping->get();

        return $this;
    }

    /**
     * Set the product's shipping height.
     *
     * @param  Closure|Measure|double|array $shippingHeight
     * @param  string $unit
     * @return $this
     */
    public function shippingHeight($shippingHeight, $unit = 'cm')
    {
        if(is_numeric($shippingHeight)){
            $shippingHeight = (new Measure)->value($shippingHeight)->unit($unit);
        }elseif(is_array($shippingHeight)){
            $shippingHeight = (new Measure)->with($shippingHeight);
        }elseif (is_callable($shippingHeight)) {
            $callback = $shippingHeight;
            $shippingHeight = (new Measure)->unit($unit); // default
            $callback($shippingHeight);
        }elseif(!($shippingHeight instanceof Measure)){
            throw new \MOIREI\GoogleMerchantApi\Exceptions\InvalidMeasureInput;
        }

        $this->attributes[ 'shippingHeight' ] = $shippingHeight->get();

        return $this;
    }
    /**
     * Set the product's shipping length.
     *
     * @param  Closure|Measure|double|array $shippingLength
     * @param  string $unit
     * @return $this
     */
    public function shippingLength($shippingLength, $unit = 'cm')
    {
        if(is_numeric($shippingLength)){
            $shippingLength = (new Measure)->value($shippingLength)->unit($unit);
        }elseif(is_array($shippingLength)){
            $shippingLength = (new Measure)->with($shippingLength);
        }elseif (is_callable($shippingLength)) {
            $callback = $shippingLength;
            $shippingLength = (new Measure)->unit($unit); // default
            $callback($shippingLength);
        }elseif(!($shippingLength instanceof Measure)){
            throw new \MOIREI\GoogleMerchantApi\Exceptions\InvalidMeasureInput;
        }

        $this->attributes[ 'shippingLength' ] = $shippingLength->get();

        return $this;
    }

    /**
     * Set the product's shipping weight.
     *
     * @param  Closure|Measure|double|array $shippingWeight
     * @param  string $unit
     * @return $this
     */
    public function shippingWeight($shippingWeight, $unit = 'kg')
    {
        if(is_numeric($shippingWeight)){
            $shippingWeight = (new Measure)->value($shippingWeight)->unit($unit);
        }elseif(is_array($shippingWeight)){
            $shippingWeight = (new Measure)->with($shippingWeight);
        }elseif (is_callable($shippingWeight)) {
            $callback = $shippingWeight;
            $shippingWeight = (new Measure)->unit($unit); // default
            $callback($shippingWeight);
        }elseif(!($shippingWeight instanceof Measure)){
            throw new \MOIREI\GoogleMerchantApi\Exceptions\InvalidMeasureInput;
        }

        $this->attributes[ 'shippingWeight' ] = $shippingWeight->get();

        return $this;
    }

    /**
     * Append the product's tax.
     *
     * @param  Closure|Taxes|array $tax
     * @return $this
     */
    public function taxes($tax)
    {
        if (is_callable($tax)) {
            $callback = $tax;
            $callback($tax = new Taxes);
        }elseif(is_array($tax)){
            $tax = (new Taxes)->with($tax);
        }
        elseif(!($tax instanceof Taxes)){
            throw new \MOIREI\GoogleMerchantApi\Exceptions\InvalidTaxInput;
        }

        $this->attributes[ 'taxes' ][] = $tax->get();

        return $this;
    }

    /**
     * Set the product's unit pricing base measure.
     *
     * @param  Closure|Measure|long|array $value
     * @param  string $unit
     * @return $this
     */
    public function unitPricingBaseMeasure($value, $unit = null)
    {
        if(is_numeric($value)){
            $value = (new Measure)->value($value)->unit($unit);
        }elseif(is_array($value)){
            $value = (new Measure)->with($value);
        }elseif (is_callable($value)) {
            $callback = $value;
            $value = (new Measure)->unit($unit); // default
            $callback($value);
        }elseif(!($value instanceof Measure)){
            throw new \MOIREI\GoogleMerchantApi\Exceptions\InvalidMeasureInput;
        }

        $this->attributes[ 'unitPricingBaseMeasure' ] = $value->get();

        return $this;
    }

    /**
     * Set the product's unit pricing measure.
     *
     * @param  Closure|Measure|double|array $value
     * @param  string $unit
     * @return $this
     */
    public function unitPricingMeasure($value, $unit = null)
    {
        if(is_numeric($value)){
            $value = (new Measure)->value($value)->unit($unit);
        }elseif(is_array($value)){
            $value = (new Measure)->with($value);
        }elseif (is_callable($value)) {
            $callback = $value;
            $value = (new Measure)->unit($unit); // default
            $callback($value);
        }elseif(!($value instanceof Measure)){
            throw new \MOIREI\GoogleMerchantApi\Exceptions\InvalidMeasureInput;
        }

        $this->attributes[ 'unitPricingMeasure' ] = $value->get();

        return $this;
    }

    /**
     * Set the product's sale price effective date.
     *
     * Date range during which the item is on sale.
     *
     * @param  Carbon\Carbon|string $until
     * @param  Carbon\Carbon|string|null $from
     * @return $this
     */
    public function salePriceEffectiveDate($until, $from = null)
    {

        if( !($until instanceof Carbon) ){
            $until = new Carbon($until);
        }

        if(is_null($from)){
            $from = now();
        }elseif(!($from instanceof Carbon)){
            $from = new Carbon($from);
        }

        $this->attributes[ 'salePriceEffectiveDate' ] = $from->format('Y-m-d') . 'T' . $from->format('h:i:s') . '/' .
                                                        $until->format('Y-m-d') . 'T' . $until->format('h:i:s');

        return $this;
    }

    /**
     * Set the product's sizes.
     *
     * @param  array $sizes
     * @return $this
     */
    public function sizes($sizes)
    {
        if(is_array($sizes)){
            $this->attributes[ 'sizes' ] = $sizes;
        }else{
            $this->attributes[ 'sizes' ][] = $sizes;
        }

        return $this;
    }

    /**
     * Set a custom value.
     *
     * @param  string|array $custom
     * @param  mix $value
     * @param  string $type
     * @return $this
     */
    public function custom($custom, $value = null, $type = null)
    {
        $name = '';

        if(is_array($custom)){
            $name = $custom['name'];
            $value = $custom['value'];
            $type = $custom['type']?? null;
        }else{
            $name = $custom;
        }

        if(is_null($type)){
            if(is_int($value)){
                $type = 'int';
            }elseif(is_float($value)){
                $type = 'float';
            }elseif(is_bool($value)){
                $type = 'boolean';
            }else{
                $type = 'text';
            }
        }else{
            if(!in_array($type, [
                'boolean', 'datetimerange', 'float', 'group',
                'int', 'price', 'text', 'time', 'url',
            ])){
                $type = 'text';
            }
        }

        $this->attributes[ 'customAttributes' ][] = [
            'name' => $name,
            'type' => $type,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * Set the product's custom values.
     *
     * @param  array $customValues
     * @return $this
     */
    public function customValues(array $customValues)
    {
        foreach($customValues as $customValue){
            $this->custom($customValues);
        }

        return $this;
    }

}
