<?php

namespace MOIREI\GoogleMerchantApi\Contents;

use Closure;

class BaseContent
{
    /**
     * The content's attributes.
     *
     * @var string kind
     *
     * @var array
     */
    public $attributes;

    /**
     * Names of alloed attributes
     *
     * @var array
     */
    protected $allowed_attributes = [];

	/**
	 * Setup the product shipping content
	 */
    public function __construct(){
        //
    }

    public function __call($name, $arguments) {
        if(in_array($name, $this->allowed_attributes)){
            $this->attributes[ $name ] = $arguments[0];
        }else{
            throw new \BadMethodCallException("Instance method Content->$name() doesn't exist");
        }

        return $this;
    }

    public function __get($attribute){

        // Mutable attributes
        $attributeFunc = 'get' . ucfirst($attribute);
        if(method_exists($this, $attributeFunc)) return $this->$attributeFunc();

        if(isset($this->attributes[ $attribute ]) && !empty($this->attributes[ $attribute ])){
            return $this->attributes[ $attribute ];
        }

        return null;
    }


    /**
     * Batch fill with array
     *
     * @param array $attributes
     */
    public function with($attributes){

        $attributes = collect($attributes)->only($this->allowed_attributes)->all();

        foreach($attributes as $key => $attribute){
            $this->attributes[ $key ] = $attribute;
        }

        return $this;
    }

    /**
     * Format and retrieve the content variables
     *
     * @return array
     */
    public function get(){
        return array_filter(collect($this->attributes)->only($this->allowed_attributes)->all(), function($value){
            return !($value === null) && !($value === '') && !($value === []);
        });
    }

    /**
     * Format and retrieve the content variables
     *
     * @return array
     */
    public function all(){
        $attributes = $this->attributes;
        foreach($attributes as $key => $attribute){
            $attributeFunc = 'get' . ucfirst($key);
            if(method_exists($this, $attributeFunc)){
                $attributes[ $key ] = $this->$attributeFunc();
            }
        }
        return array_filter($attributes, function($value){
            return !($value === null) && !($value === '') && !($value === []);
        });
    }

    /**
     * Get content attribute data
     *
     * @param string $attribute
     * @param mix $default
     * @return mix
     */
    public function dataGet($attribute, $default = null){
        if(isset($this->attributes[ $attribute ]) && !empty($this->attributes[ $attribute ])){
            return $this->attributes[ $attribute ];
        }
        return $default;
    }


}
