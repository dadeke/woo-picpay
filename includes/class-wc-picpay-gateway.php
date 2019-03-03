<?php
/**
 * WooCommerce PicPay Gateway class
 *
 * @package Woo_PicPay/Classes/Gateway
 * @version 1.0.0
 */

if(!defined('ABSPATH')) {
	exit;
}

/**
 * WooCommerce PicPay gateway.
 */
class WC_PicPay_Gateway extends WC_Payment_Gateway {

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		$this->id                 = 'picpay';
		$this->icon               = apply_filters('woo_picpay_icon', plugins_url('assets/images/picpay.svg', plugin_dir_path(__FILE__)));
		$this->method_title       = __('PicPay', 'woo-picpay');
		$this->method_description = __('Accept payments using the PicPay.', 'woo-picpay');
		$this->order_button_text  = __('Proceed to payment', 'woo-picpay');

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables.
		$this->title        = $this->get_option('title');
		$this->description  = $this->get_option('description');
		$this->picpay_token = $this->get_option('picpay_token');
		$this->seller_token = $this->get_option('seller_token');
		$this->debug        = $this->get_option('debug');

		// Active logs.
		if($this->debug == 'yes') {
			if(function_exists('wc_get_logger')) {
				$this->log = wc_get_logger();
			} else {
				$this->log = new WC_Logger();
			}
		}

		// Set the API.
		$this->api = new WC_PicPay_API($this);

		// Main actions.
		add_action('woocommerce_api_wc_picpay_gateway', array($this, 'process_callback'));
		add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
		add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));
		add_action('woocommerce_order_status_cancelled', array($this, 'cancel_payment'));
		add_action('woocommerce_order_status_refunded', array($this, 'cancel_payment'));
	}

	/**
	 * Returns a bool that indicates if currency is amongst the supported ones.
	 *
	 * @return bool
	 */
	public function using_supported_currency() {
		return get_woocommerce_currency()  === 'BRL';
	}

	/**
	 * Returns a value indicating the the Gateway is available or not. It's called
	 * automatically by WooCommerce before allowing customers to use the gateway
	 * for payment.
	 *
	 * @return bool
	 */
	public function is_available() {
		// Test if is valid for use.
		$available = $this->get_option('enabled') === 'yes' && $this->picpay_token !== '' && $this->seller_token !== '' && $this->using_supported_currency();

		if(!class_exists('Extra_Checkout_Fields_For_Brazil')) {
			$available = false;
		}

		return $available;
	}

	/**
	 * Get log.
	 *
	 * @return string
	 */
	protected function get_log_view() {
		if(defined('WC_VERSION') && version_compare(WC_VERSION, '2.2', '>=')) {
			return '<a href="' . esc_url(admin_url('admin.php?page=wc-status&tab=logs&log_file=' . esc_attr($this->id) . '-' . sanitize_file_name(wp_hash($this->id)) . '.log')) . '">' . __('System Status &gt; Logs', 'woo-picpay') . '</a>';
		}

		return '<code>woocommerce/logs/' . esc_attr($this->id) . '-' . sanitize_file_name(wp_hash($this->id)) . '.txt</code>';
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'              => array(
				'title'   => __('Enable/Disable', 'woo-picpay'),
				'type'    => 'checkbox',
				'label'   => __('Enable PicPay', 'woo-picpay'),
				'default' => 'yes',
			),
			'title'                => array(
				'title'       => __('Title', 'woo-picpay'),
				'type'        => 'text',
				'description' => __('This controls the title which the user sees during checkout.', 'woo-picpay'),
				'desc_tip'    => true,
				'default'     => __('PicPay', 'woo-picpay'),
			),
			'description'          => array(
				'title'       => __('Description', 'woo-picpay'),
				'type'        => 'textarea',
				'description' => __('This controls the description which the user sees during checkout.', 'woo-picpay'),
				'default'     => __('Pay via PicPay', 'woo-picpay'),
			),
			'picpay_token'         => array(
				'title'       => __('PicPay Token', 'woo-picpay'),
				'type'        => 'text',
				/* translators: %s: link to PicPay settings */
				'description' => sprintf(__('Please enter your PicPay token. This is needed to process the payments and notifications. Is possible generate a new token %s.', 'woo-picpay'), '<a href="https://lojista.picpay.com/dashboard/ecommerce-token" target="_blank">' . __('here', 'woo-picpay') . '</a>'),
				'default'     => '',
			),
			'seller_token'         => array(
				'title'       => __('Seller Token', 'woo-picpay'),
				'type'        => 'text',
				'description' => __('Please enter your Seller token.', 'woo-picpay'),
				'default'     => '',
			),
			'debug'                => array(
				'title'       => __('Debug Log', 'woo-picpay'),
				'type'        => 'checkbox',
				'label'       => __('Enable logging', 'woo-picpay'),
				'default'     => 'no',
				/* translators: %s: log page link */
				'description' => sprintf(__('Log PicPay events, such as API requests, inside %s', 'woo-picpay'), $this->get_log_view()),
			),
		);
	}
	
	/**
	 * Admin page.
	 */
	public function admin_options() {
		include dirname(__FILE__) . '/admin/views/html-admin-page.php';
	}
	
	/**
	 * Send email notification.
	 *
	 * @param string $subject Email subject.
	 * @param string $title   Email title.
	 * @param string $message Email message.
	 */
	protected function send_email($subject, $title, $message) {
		$mailer = WC()->mailer();

		$mailer->send(get_option('admin_email'), $subject, $mailer->wrap_message($title, $message));
	}
	
	/**
	 * Process the payment and return the result.
	 *
	 * @param  int $order_id Order ID.
	 * @return array
	 */
	public function process_payment($order_id) {
		$response = array();
		$order = wc_get_order($order_id);
		
		// Check if PicPay PaymentURL already exists. 
		$response['url'] = $order->get_meta('PicPay_PaymentURL');
		if(!$response['url']) {
			$response = $this->api->do_checkout_request($order);
			
			if($response['url']) {
				$order->add_meta_data('PicPay_PaymentURL', $response['url']);
				$order->save();
			}
		}
		
		if($response['url']) {
			// Remove cart.
			WC()->cart->empty_cart();
			
			$order->add_order_note(__('PicPay: The buyer initiated the transaction, but so far the PicPay not received any payment information.', 'woo-picpay'));
			
			return array(
				'result'   => 'success',
				'redirect' => $response['url'],
			);
		}
		else {
			foreach($response['error'] as $error) {
				wc_add_notice($error, 'error');
			}
        
			return array(
				'result'   => 'fail',
				'redirect' => '',
			);
		}
	}
	
	/**
	 * Output for the order received page.
	 *
	 * @param int $order_id Order ID.
	 */
	public function receipt_page($order_id) {
		$order = wc_get_order($order_id);
		$payment_url = $order->get_meta('PicPay_PaymentURL');
		
		if($order->status == 'pending') {
			if(!empty($payment_url)) {
				wp_redirect($payment_url, 302);
			}
			else {
				include dirname(__FILE__) . '/views/html-receipt-page-error.php';
			}
		}
	}
	
	/**
	 * Process callback.
	 */
	public function process_callback() {
		@ob_clean();
		$payment = $this->api->process_callback();
		if(is_array($payment)) {
			$order = wc_get_order($payment['referenceId']);
			$cancellation_id = $order->get_meta('cancellationId');
			
			if(($payment['status'] == 'refunded') && empty($cancellation_id)) {
				$payment['cancellationId'] = __('Payment refunded directly by PicPay.', 'woo-picpay');
			}
			
			$this->update_order_status($payment);
		}
		exit;
	}
	
	/**
	 * Save payment meta data.
	 *
	 * @param WC_Order $order Order instance.
	 * @param array $payment Payment Status.
	 */
	protected function save_payment_meta_data($order, $payment) {
		foreach($payment as $key => $value) {
			if(($key != 'referenceId') && ($key != 'status')) {
				$order->add_meta_data($key, $value);
			}
		}
		$order->save();
	}
	
	/**
	 * Update order status.
	 *
	 * @param array $payment Payment Status.
	 */
	public function update_order_status($payment) {
		$id = intval(str_replace($this->invoice_prefix, '', $payment['referenceId']));
		$order = wc_get_order($id);

		// Check if order exists.
		if(!$order) {
			return;
		}
		
		$order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;

		if($this->debug == 'yes') {
			$this->log->add($this->id, 'PicPay payment status for order ' . $order->get_order_number() . ' is: ' . $payment['status']);
		}
		
		// Save meta data.
		$this->save_payment_meta_data($order, $payment);
		
		switch($payment['status']) {
			case 'expired':
				$order->update_status('cancelled', __('PicPay: Payment expired.', 'woo-picpay'));
				
				break;
			case 'analysis':
				$order->update_status('on-hold', __('PicPay: Payment under review.', 'woo-picpay'));
				wc_reduce_stock_levels($order_id);
				
				break;
			case 'paid':
				if($order->get_status() == 'created') {
					wc_reduce_stock_levels($order_id);
				}
				$order->update_status('processing', __('PicPay: Payment approved.', 'woo-picpay'));
			
				break;
			case 'completed':
				$order->add_order_note(__('PicPay: Payment completed and credited to your account.', 'woo-picpay'));
				
				break;
			case 'refunded':
				if($order->get_status() != 'refunded') { // Prevents repeat refunded.
					$order->update_status('refunded', __('PicPay: Payment refunded.', 'woo-picpay'));
					wc_increase_stock_levels($order_id);
				}
				else {
					$order->add_order_note(__('PicPay: Payment refunded.', 'woo-picpay'));
				}
				
				$this->send_email(
					/* translators: %s: order number */
					sprintf(__('Payment for order %s refunded', 'woo-picpay'), $order->get_order_number()),
					__('Payment refunded', 'woo-picpay'),
					/* translators: %s: order number */
					sprintf(__('Order %s has been marked as refunded by PicPay.', 'woo-picpay' ), $order->get_order_number())
				);
			
				break;
			case 'chargeback':
				$order->update_status('refunded', __('PicPay: Payment chargeback.', 'woo-picpay'));
				
				break;
			default:
				break;
		}
	}
	
	/**
	 * Cancel payment.
	 *
	 * @param  int $order_id Order ID.
	 */
	public function cancel_payment($order_id) {
		$order = wc_get_order($order_id);
		
		$cancellation_id = $order->get_meta('cancellationId');
		
		if(empty($cancellation_id)) { // Prevents repeat refunded.
			$payment = $this->api->do_payment_cancel($order);
				
			if(is_array($payment)) {
				$this->save_payment_meta_data($order, $payment);
			}
		}
	}
}
