<?php
/**
 * Admin options screen.
 *
 * @package Woo_PicPay/Admin/Settings
 */
if(!defined('ABSPATH')) {
	exit;
}
?>

<h3><?php echo esc_html($this->method_title); ?></h3>

<?php
	if($this->get_option('enabled' ) == 'yes') {
		if(!$this->using_supported_currency() && !class_exists('woocommerce_wpml')) {
			include dirname(__FILE__) . '/html-notice-currency-not-supported.php';
		}

		if(($this->picpay_token === '') || ($this->seller_token === '')) {
			include dirname(__FILE__) . '/html-notice-token-missing.php';
		}
	}
?>

<?php echo wpautop($this->method_description); ?>

<?php include dirname(__FILE__) . '/html-admin-help-message.php'; ?>

<table class="form-table">
	<?php $this->generate_settings_html(); ?>
</table>
