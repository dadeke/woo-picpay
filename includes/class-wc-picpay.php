<?php
/**
 * WooCommerce PicPay main class
 *
 * @package Woo_PicPay
 */

/**
 * WooCommerce bootstrap class.
 */
class WC_PicPay {

	/**
	 * Initialize the plugin public actions.
	 */
	public static function init() {
		// Load plugin text domain.
		add_action('init', array(__CLASS__, 'load_plugin_textdomain'));

		// Checks with WooCommerce is installed.
		if(class_exists('WC_Payment_Gateway')) {
			self::includes();
			
			add_filter('woocommerce_payment_gateways', array(__CLASS__, 'add_gateway'));
			add_filter('woocommerce_available_payment_gateways', array(__CLASS__, 'hides_when_is_outside_brazil'));
			add_filter('plugin_action_links_' . plugin_basename(WC_PICPAY_PLUGIN_FILE), array(__CLASS__, 'plugin_action_links'));
			
			if(is_admin()) {
				add_action('admin_notices', array(__CLASS__, 'ecfb_missing_notice'));
			}
		} else {
			add_action('admin_notices', array(__CLASS__, 'woocommerce_missing_notice'));
		}
	}
	
	/**
	 * Load the plugin text domain for translation.
	 */
	public static function load_plugin_textdomain() {
		load_plugin_textdomain('woo-picpay', false, dirname(plugin_basename(WC_PICPAY_PLUGIN_FILE)) . '/languages/');
	}
	
	/**
	 * Action links.
	 *
	 * @param array $links Action links.
	 *
	 * @return array
	 */
	public static function plugin_action_links($links) {
		$plugin_links   = array();
		$plugin_links[] = '<a href="' . esc_url(admin_url('admin.php?page=wc-settings&tab=checkout&section=picpay')) . '">' . __('Settings', 'woo-picpay') . '</a>';

		return array_merge($plugin_links, $links);
	}
	
	/**
	 * Includes.
	 */
	private static function includes() {
		include_once dirname(__FILE__) . '/class-wc-picpay-api.php';
		include_once dirname(__FILE__) . '/class-wc-picpay-gateway.php';
	}
	
	/**
	 * Add the gateway to WooCommerce.
	 *
	 * @param  array $methods WooCommerce payment methods.
	 *
	 * @return array Payment methods with PicPay.
	 */
	public static function add_gateway($methods) {
		$methods[] = 'WC_PicPay_Gateway';

		return $methods;
	}
	
	/**
	 * Hides the PicPay with payment method with the customer lives outside Brazil.
	 *
	 * @param   array $available_gateways Default Available Gateways.
	 *
	 * @return  array                     New Available Gateways.
	 */
	public static function hides_when_is_outside_brazil($available_gateways) {
		// Remove PicPay gateway.
		if(isset($_REQUEST['country']) && $_REQUEST['country'] !== 'BR') {
			unset($available_gateways['picpay']);
		}

		return $available_gateways;
	}
	
	/**
	 * WooCommerce Extra Checkout Fields for Brazil notice.
	 */
	public static function ecfb_missing_notice() {
		if(!class_exists('Extra_Checkout_Fields_For_Brazil')) {
			include dirname(__FILE__) . '/admin/views/html-notice-missing-ecfb.php';
		}
	}
	
	/**
	 * WooCommerce missing notice.
	 */
	public static function woocommerce_missing_notice() {
		include dirname(__FILE__) . '/admin/views/html-notice-missing-woocommerce.php';
	}
}