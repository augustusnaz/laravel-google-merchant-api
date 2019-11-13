<?php

namespace MOIREI\GoogleMerchantApi\Api;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Closure;
use GuzzleHttp\Promise\Promise;

abstract class AbstractApi{

    /**
     * Set request endpoint.
     *
     * @var  string $endpoint
     */
    protected $endpoint;

    /**
     * The GuzzleHttp client.
     *
     * @var  \GuzzleHttp\Client $client
     */
	protected $client;

    /**
     * The request method.
     *
     * @var  string $request_method
     */
	protected $request_method;

    /**
     * The request path.
     *
     * @var  string $request_path
     */
	protected $request_path;

    /**
     * The request params.
     *
     * @var  array $request_params
     */
	protected $request_params;

    /**
     * The request body.
     *
     * @var  array $request_body
     */
	protected $request_body;

    /**
     * The request callback on success.
     *
     * @var  Closure $then
     */
	protected $then;

    /**
     * The request callback on unsuccessful.
     *
     * @var  Closure $otherwise
     */
	protected $otherwise;

    /**
     * The request callback on failure.
     *
     * @var  Closure $catch
     */
	protected $catch;

    /**
     * If the request asynchronous.
     *
     * @var  boolean $async
     */
	protected $async = true;

	/**
	 * Set the endpoint, merchantId and client
	 *
	 * @param string $endpoint top-level endpoint, e.g. `products`
	 * @param string $mode 'production', 'sandbox'
	 */
	public function __construct( $endpoint, $mode = 'production' ) {

		$this->endpoint = $endpoint;

		$this->merchantId = config('laravel-google-merchant-api.merchant_id', '');

		$version = config('laravel-google-merchant-api.version', 'v2');
		if($mode === 'sandbox'){
			$version = $version . 'sandbox';
		}

		$client_config = collect(config('laravel-google-merchant-api.client_config'))->only([
			'timeout', 'headers', 'proxy',
			'allow_redirects', 'http_errors', 'decode_content', 'verify', 'cookies',
		])->filter()->all();
		$client_config['base_uri'] = "https://www.googleapis.com/content/$version/$this->merchantId/";

		$client_config['headers'] = array_merge($client_config['headers']?? [], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);

		$client_credentials_path = base_path( config('laravel-google-merchant-api.client_credentials_path') );

		if((strpos($client_credentials_path, '.json') !== false) && file_exists($client_credentials_path)){
			$client = new \Google_Client();
			$client->setHttpClient( new Client($client_config) );

			if($app_name = config('laravel-google-merchant-api.app_name', false)){
				$client->setApplicationName($app_name);
			}

			$client->setAuthConfig( $client_credentials_path );
			$client->addScope('https://www.googleapis.com/auth/content');

			$this->client = $client->authorize();
		}else{
			$this->client = new Client($client_config);
		}

	}

	/**
	 * Set the async option.
	 *
	 * @param boolean $sync
	 */
	public function sync($sync = true){
		$this->async = !$sync;

		return $this;
	}

	/**
	 * Set the arguments for the request, required:
	 *
	 * `method`
	 * `path`
	 *
	 * optional:
	 *
	 * `params`
	 * `body`
	 *
	 * @param array $args
	 */
	protected function setRequestArgs( $args ) {
		if( isset( $args['method'] ) ){
			$this->request_method = $args['method'];
		}
		if( isset( $args['path'] ) ){
			$this->request_path = $args['path'];
		}
		if( isset( $args['params'] ) ){
			$this->request_params = $args['params'];
		}
		if( isset( $args['body'] ) ){
			$this->request_body = $args['body'];
		}
	}


	/**
	 * Get the API url
	 *
	 * @return string
	 */
	protected function getUrl() {
        return empty( $this->request_path ) ? $this->endpoint : $this->endpoint . '/' . $this->request_path;
	}


	/**
	 * Return the request data, either query parameters (for GET/DELETE requests)
	 * or the request body (for PUT/POST requests)
	 *
	 * @return array
	 */
	protected function getRequestData() {
		if( 'GET' === $this->request_method || 'DELETE' === $this->request_method ){
			return [
				'param' => json_encode( $this->request_params )
			];
		}else{
			return [
				'body' => json_encode( $this->request_body )
			];
		}
	}


	/**
	 * Perform the request and return the response
	 *
	 * @return mix
	 * @throws \GuzzleHttp\Exception\ClientException
	 */
	protected function execRequest() {

		if($this->async){

			$promise = new Promise();
			$promise->then(

				function ($response){
					if($response->getStatusCode() === 200){
						if (is_callable($this->then)) {
							$callback = $this->then;
							$callback( json_decode($response->getBody(), true) );
						}
					}else{
						if (is_callable($this->otherwise)) {
							$callback = $this->otherwise;
							$callback($response);
						}
					}
				},

				function ($e) {
					if (is_callable($this->catch)) {
						$callback = $this->catch;
						$callback($e);
					}else{
						throw $e;
					}
				}
			);
		}

		try{

			$response = $this->client->request( $this->request_method, $this->getUrl(), $this->getRequestData() );

			if($this->async){
				$promise->resolve($response);
				return $this;
			}

			return $response;
		}catch(\GuzzleHttp\Exception\ClientException $e){
			if($this->async){
				$promise->reject($e);
				return $this;
			}else{
				throw $e;
			}
		}

	}

	/**
	 * POST resource
	 *
	 * POST /resource
	 * POST /resource/#{id}
	 *
	 * @param array $params
	 * @return mix
	 * @throws \GuzzleHttp\Exception\ClientException
	 */
	public function post( $params = array() ) {

		$this->setRequestArgs([
			'method' => 'POST',
			// 'params' => $params,
			'body' => $params,
		]);

		$this->clearCallbacks();

		return $this->execRequest();
	}

	/**
	 * Get resource
	 *
	 * GET /{resource}
	 * GET /{resource}/#{id}
	 *
	 * @param null|int $id resource ID or null to get all
	 * @return mix
	 * @throws \GuzzleHttp\Exception\ClientException
	 */
	public function get( $id = null, $params = array() ) {

		$this->setRequestArgs([
			'method' => 'GET',
			'path'   => $id,
			'params' => $params,
		]);

		$this->clearCallbacks();

		return $this->execRequest();
	}


	/**
	 * Delete a resource. Creates a Promise
	 *
	 * DELETE /{resource}/#{id}
	 *
	 * @param int $id product ID
	 * @return instance
	 */
	// public function delete( $id ) {

	// 	$this->setRequestArgs([
	// 		'method' => 'DELETE',
	// 		'path'   => $id,
	// 	]);

	// 	$this->clearCallbacks();

	//	return $this->execRequest();
	// }

	/**
	 * Closure callback on success for request client
	 *
	 * @param Closure $callback
	 * @return this
	 */
    public function then(Closure $callback){
		$this->then = $callback;

		return $this;
	}

	/**
	 * Closure callback on unsuccess for request client
	 *
	 * @param Closure $callback
	 * @return this
	 */
    public function otherwise(Closure $callback){
        $this->otherwise = $callback;

		return $this;
    }

	/**
	 * Closure callback on exception for request client
	 *
	 * @param Closure $callback
	 * @return this
	 */
    public function catch(Closure $callback){
        $this->catch = $callback;

		return $this;
	}

	/**
	 * Clear callbacks
	 */
	protected function clearCallbacks(){
		$this->then = null;
		$this->otherwise = null;
		$this->catch = null;
	}

}