<?php

namespace Payments\Custom;

use Throwable;
use ErrorException;
use Ollyo\PaymentHub\Core\Support\Arr;
use Ollyo\PaymentHub\Core\Support\System;
use GuzzleHttp\Exception\RequestException;
use Ollyo\PaymentHub\Core\Payment\BasePayment;



class Custompayment extends BasePayment
{

	protected $client;
	/**
	 * Checks if all required configuration keys are present and not empty.
	 *
	 * This method ensures that the necessary configuration settings are available
	 * and properly set up before proceeding with any operations that depend on them.
	 *
	 * @return bool Returns true if all required configuration keys are present and not empty, otherwise false.
	 */
	public function check(): bool
	{
		$configKeys = Arr::make(['secret_key', 'public_key', 'mode']);

		$isConfigOk = $configKeys->every(function($key) {
			return $this->config->has($key) && !empty($this->config->get($key));
		});

		return $isConfigOk;
	}


	/**
	 * Initializes the necessary configurations for the custom payment gateway.
	 *
	 * This method is used to set up any initial configurations or variables required for the custom payment gateway to 
	 * function properly such as initializing API keys, setting headers, or configuring payment methods.
	 * 
	 * For Example: This method initializes the client by combining the `secret_key` and `client_id` from the configuration 
	 * and stores them in the `$client` property. 
	 * 
 	 * It demonstrates a basic setup for authenticating the custom payment gateway. 
	 */
	public function setup(): void
	{
		try {
			$this->client = "{$this->config->get('secret_key')}:{$this->config->get('client_id')}";
		} catch (Throwable $error) {
			throw $error;
		}
	}


	/**
	 * Sets the payment data according to the payment gateway preferences and structures it for the parent.
	 *
	 * This method allows the user to configure the payment data based on their preferred payment gateway settings. 
	 * The data, which is passed from Tutor, is structured according to the gateway's requirements and then set 
	 * using the parent `setData` method. If an error occurs during this process, an exception will be thrown.
	 *
	 * @param  object 		$data 	The data to set on the object.
	 * @throws Throwable 			If the parent `setData` method throws an error.
	 */
	public function setData($data): void
	{
		try {
			// Structure the payment data according to the preferred gateway settings.
			// This data will be configured as per the gateway's needs.
			$structuredData = $this->prepareData($data);

			// Set the structured data to the parent class for further processing.
			parent::setData($structuredData);

		} catch (Throwable $error) {
			throw $error;
		}
	}

	/**
	 * Prepares the payment data according to the payment gateway's preferences.
	 */
	private function prepareData($data)
	{
		// Logic to structure or format the payment data as required by the gateway.
		// This could involve reformatting fields, adding necessary parameters, etc.
		return $data; // Example: return the data as is, or modify as needed.
	}
	

	/**
	 * Creates the payment process by sending the necessary data to the payment gateway.
	 *
	 * This function will send payment data to the payment gateway, initiating the payment process.
	 * The user is required to implement this method according to their specific payment gateway preferences.
	 * The required configuration values such as `secret_key`, `public_key`, `success_url`, `cancel_url`,
	 * and `webhook_url` are fetched using `$this->config->get('property_name')`. 
	 * 
	 * Additionally, the payment data set in the `setData()` function will be retrieved using `$this->getData()`.
	 * After the payment data is sent, the user may choose to redirect the customer to a specific URL or any other option 
	 * based on the payment gateway's preference.
	 *
	 * In this demo, the user will be redirected to a sample URL (`https://tutorlms.com/`) as a placeholder.
	 *
	 */
	public function createPayment()
	{
		try {
			
			// Get necessary configuration values from the gateway settings
			$secret_key  = $this->config->get('secret_key');
			$public_key  = $this->config->get('public_key');
			$client_id   = $this->config->get('client_id');
			$success_url = $this->config->get('success_url');
			$cancel_url  = $this->config->get('cancel_url');
			$webhook_url = $this->config->get('webhook_url');

			// Retrieve payment data that was set using setData() earlier
			$payment_data = $this->getData();

			// Send the payment data to the payment gateway and process the payment
			// Implement actual payment logic here, e.g., API call to the gateway
			// And Redirect the user according to the payment gateway preferences
			header("Location: https://tutorlms.com/");

		} catch (RequestException $error) {
			throw new ErrorException($error->getResponse()->getBody());
		}
	}

	/**
	 * 
 	 * Verifies and processes the order data received from the payment gateway.
	 *
	 * This function is used to handle webhook notifications or any other data sent by the payment gateway.
	 * The `payload` is an associative array with (object) ['get' => $_GET, 'post' => $_POST, 'server' => $_SERVER, 'stream' => file_get_contents('php://input')]
 	 *
	 * The method processes the received data and prepares the order data. 
	 * It returns an object that contains order information such as order ID, payment status, transaction ID, 
	 * payment_payload, fees, and earnings.
	 * 
	 * Users will extend this function to handle specific fields or conditions based on their payment gateway's
	 * webhook structure.
	 *
	 * User should set the `$returnData` object with the applicable data from the `payload` object such as
	 * payment status, transaction ID, error reasons, etc.
	 *
	 * @param  object $payload 	An associative array with (object) ['get' => $_GET, 'post' => $_POST, 'server' => $_SERVER, 'stream' => file_get_contents('php://input')]
	 * @return object
	 * @throws Throwable
	 */
	public function verifyAndCreateOrderData(object $payload): object
	{
		// The information of `$_GET` variable that contains data from the URL query string (i.e., parameters appended to the URL in a GET request).
		$get_data   = $payload->get;  

		// The information of $_POST variable that contains data sent via an HTTP POST request.
		$post_data  = $payload->post;
		
		//The information of $_SERVER variable that contains information about the server environment and request headers.
		$server_variables = $payload->server;
		
		//It's a PHP stream that allows access to the raw POST data (without parsing).
		$stream = $payload->stream;
		
		$returnData = System::defaultOrderData();

		try {

			// Validate the necessary checks based on the payment gateway's preferences to ensure that the $payload information is coming from the provided payment gateway.

			// Then set the `$returnData` object with the applicable data from the `payload` object and return it to Tutor.
			$returnData->id 					= '';
			$returnData->payment_status 		= '';
			$returnData->payment_error_reason 	= '';
			$returnData->transaction_id 		= '';
			$returnData->payment_payload 		= '';
			$returnData->fees 					= '';
			$returnData->earnings 				= '';

			return $returnData;
		} catch (Throwable $error) {
			throw $error;
		}
	}
}