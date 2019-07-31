<?php
/**
 * WooCommerce PicPay API class
 *
 * @package Woo_PicPay/Classes/API
 * @version 1.1.2
 */

if(!defined('ABSPATH')) {
	exit;
}

/**
 * WooCommerce PicPay API.
 */
class WC_PicPay_API {

	/**
	 * Gateway class.
	 *
	 * @var WC_PicPay_Gateway
	 */
	protected $gateway;

	/**
	 * Constructor.
	 *
	 * @param WC_PicPay_Gateway $gateway Payment Gateway instance.
	 */
	public function __construct($gateway = null) {
		$this->gateway = $gateway;
	}
	
	/**
	 * Get the payment URL.
	 *
	 * @return string.
	 */
	protected function get_payment_url() {
		return 'https://appws.picpay.com/ecommerce/public/payments';
	}
	
	/**
	 * Get the status URL.
	 *
	 * @param  string $order_id Order ID.
	 *
	 * @return string.
	 */
	protected function get_status_url($order_id) {
		return 'https://appws.picpay.com/ecommerce/public/payments/' . $order_id . '/status';
	}
	
	/**
	 * Get the cancellation URL.
	 *
	 * @param  string $order_id Order ID.
	 *
	 * @return string.
	 */
	protected function get_cancellation_url($order_id) {
		return 'https://appws.picpay.com/ecommerce/public/payments/' . $order_id . '/cancellations';
	}
	
	/**
	 * Do requests in the PicPay API.
	 *
	 * @param  string $url      URL.
	 * @param  string $method   Request method.
	 * @param  array  $data     Request data.
	 * @param  array  $headers  Request headers.
	 *
	 * @return array            Request response.
	 */
	protected function do_request($url, $method = 'POST', $data = array(), $headers = array()) {
		$params = array(
			'method'  => $method,
			'timeout' => 60,
		);

		if($method == 'POST' && !empty($data)) {
			$params['body'] = $data;
		}

		if(!empty($headers)) {
			$params['headers'] = $headers;
		}

		return wp_safe_remote_post($url, $params);
	}
	
	/**
	 * Get the headers.
	 *
	 * @return array.
	 */
	protected function get_request_headers() {
		return array(
						'x-picpay-token' => $this->gateway->picpay_token,
						'Content-Type' => 'application/json'
					);
	}
	
	/**
	 * Get the checkout json.
	 *
	 * @param WC_Order $order Order data.
	 * @param array    $posted Posted data.
	 *
	 * @return string
	 */
	protected function get_checkout_json($order) {
		$cellphone = $order->get_meta('_billing_cellphone');
		$document = $order->get_meta('_billing_cpf');
		
		if(empty($cellphone)) {
			$cellphone = $order->get_billing_phone();
		}
		
		if($order->get_meta('_billing_persontype') == '2') {
			$document = $order->get_meta('_billing_cnpj');
		}
		
		$buyer = array(
					'firstName' => $order->get_billing_first_name(),
					'lastName' => $order->get_billing_last_name(),
					'document' => $document,
					'email' => $order->get_billing_email(),
					'phone' => $cellphone
				);
		
		$payment = array(
					'referenceId' => $this->gateway->invoice_prefix . $order->get_id(),
					'callbackUrl' => WC()->api_request_url('WC_PicPay_Gateway'),
					'returnUrl' => $this->gateway->get_return_url($order),
					'value' => $order->get_total(),
					'buyer' => $buyer
				);
		
		return json_encode($payment);
	}
	
	/**
	 * Do checkout request.
	 *
	 * @param  WC_Order $order  Order data.
	 *
	 * @return array
	 */
	public function do_checkout_request($order) {
		// Sets the json.
		$json = $this->get_checkout_json($order);
		$body = '';
		
		if($this->gateway->debug == 'yes') {
			$this->gateway->log->add($this->gateway->id, 'Get payment request for order ' . $order->get_order_number() . ' with the following data: ' . $json);
		}
			
		$response = $this->do_request($this->get_payment_url(), 'POST', $json, $this->get_request_headers());

		if(is_wp_error($response)) {
			if($this->gateway->debug == 'yes') {
				$this->gateway->log->add($this->gateway->id, 'WP_Error in generate payment request: ' . $response->get_error_message());
			}
		}
		else {
			$body = json_decode($response['body'], true);
			
			if(json_last_error() != JSON_ERROR_NONE) {
				if($this->gateway->debug == 'yes') {
					$this->gateway->log->add($this->gateway->id, 'Error while parsing the PìcPay response: ' . print_r($response, true));
				}
			}
		}
		
		if($response['response']['code'] === 200) {
			$url = $body['paymentUrl'];

			if($this->gateway->debug == 'yes') {
				$this->gateway->log->add($this->gateway->id, 'PicPay Payment URL created with success! The URL is: ' . $url);
			}

			return array(
				'url'   => $url,
				'data'  => '',
				'error' => ''
			);
		}
		else if($response['response']['code'] === 401) {
			if($this->gateway->debug == 'yes') {
				$this->gateway->log->add($this->gateway->id, 'Invalid token settings!');
			}

			return array(
				'url'   => '',
				'data'  => '',
				'error' => array(__('Too bad! The token from the PicPay are invalids my little friend!', 'woo-picpay'))
			);
		}
		else if(($response['response']['code'] === 422) || ($response['response']['code'] === 500)) {
			if(isset($body['message'])) {
				$errors = array();

				if($this->gateway->debug == 'yes') {
					$this->gateway->log->add($this->gateway->id, 'Failed to generate the PicPay Payment URL: ' . print_r( $response, true ) );
				}

				return array(
					'url'   => '',
					'token' => '',
					'error' => array($body['message']),
				);
			}
		}
		
		if($this->gateway->debug == 'yes') {
			$this->gateway->log->add($this->gateway->id, 'Error generating the PicPay Payment URL: ' . print_r($response, true));
		}

		// Return error message.
		return array(
			'url'   => '',
			'token' => '',
			'error' => array('<strong>' . __('PicPay', 'woo-picpay') . '</strong>: ' . __('An error has occurred while processing your payment, please try again. Or contact us for assistance.', 'woo-picpay'))
		);
	}
	
	/**
	 * Process callback.
	 *
	 * @return array | boolean
	 */
	public function process_callback() {
		$payment = array();
		
		if($this->gateway->debug == 'yes') {
			$this->gateway->log->add($this->gateway->id, 'Checking CALLBACK request...');
		}
		
		// Checks the Seller Token.
		if($_SERVER['HTTP_X_SELLER_TOKEN'] != $this->gateway->seller_token) {
			if($this->gateway->debug == 'yes') {
				$this->gateway->log->add($this->gateway->id, 'Invalid CALLBACK request, invalid Seller Token.');
			}
			
			return false;
		}
		
		$payment = file_get_contents("php://input");
		$payment = json_decode($payment, true);
		if(json_last_error() != JSON_ERROR_NONE) {
			if($this->gateway->debug == 'yes') {
				$this->gateway->log->add($this->gateway->id, 'Invalid CALLBACK request: ' . print_r($payment, true));
			}
			
			return false;
		}
		else {
			if($this->gateway->debug == 'yes') {
				$this->gateway->log->add($this->gateway->id, 'CALLBACK request is OK.');
			}
		}
		
		if($this->gateway->debug == 'yes') {
			$this->gateway->log->add($this->gateway->id, 'Get payment status for order ' . $payment['referenceId']);
		}
		
		// Get payment Status.
		$res_status = $this->do_request($this->get_status_url($payment['referenceId']), 'GET', array(), $this->get_request_headers());
		$res_status = json_decode($res_status['body'], true);
		
		if(json_last_error() != JSON_ERROR_NONE) {
			if($this->gateway->debug == 'yes') {
				$this->gateway->log->add($this->gateway->id, 'Error while parsing the PicPay payment status response: ' . print_r($res_status, true));
			}
			
			return false;
		}
		
		if(array_key_exists('status', $res_status)) {
			if($this->gateway->debug == 'yes') {
				$this->gateway->log->add($this->gateway->id, 'PicPay status response is valid! The return is: ' . print_r($res_status, true));
			}
			
			$payment['status'] = $res_status['status'];
			
			return $payment;
		}
		
		if($this->gateway->debug == 'yes') {
			$this->gateway->log->add($this->gateway->id, 'PicPay payment status response: ' . print_r($res_status, true));
		}
		
		return false;
	}
	
	/**
	 * Do payment cancel.
	 *
	 * @param  WC_Order $order  Order data.
	 *
	 * @return array | boolean
	 */
	public function do_payment_cancel($order) {
		$json = '';
		$order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
		$authorization_id = $order->get_meta('PicPay_authorizationId');
		
		// Remove in future releases.
		if(empty($authorization_id)) {
			$authorization_id = $order->get_meta('authorizationId');
		}
		
		if(!empty($authorization_id)) {
			$json = json_encode(array('authorizationId' => $authorization_id));
			
			if($this->gateway->debug == 'yes') {
				$this->gateway->log->add($this->gateway->id, 'Get payment cancel for order ' . $order->get_order_number() . ' and refund with the authorizationId: ' . $authorization_id);
			}
		}
		else {
			if($this->gateway->debug == 'yes') {
				$this->gateway->log->add($this->gateway->id, 'Get payment cancel for order ' . $order->get_order_number());
			}
		}
		
		$payment = $this->do_request($this->get_cancellation_url($order_id), 'POST', $json, $this->get_request_headers());
		
		if($payment['response']['code'] === 200) {
				if($this->gateway->debug == 'yes') {
					$this->gateway->log->add($this->gateway->id, 'PicPay payment cancel response OK.');
				}
		}
		
		$payment = json_decode($payment['body'], true);
			
		if(json_last_error() != JSON_ERROR_NONE) {
			if($this->gateway->debug == 'yes') {
				$this->gateway->log->add($this->gateway->id, 'Error while parsing the PicPay payment cancel response: ' . print_r($payment, true));
			}
			
			return false;
		}
		
		return $payment;
	}
}
