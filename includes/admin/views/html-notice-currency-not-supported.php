<?php
/**
 * Admin View: Notice - Currency not supported.
 *
 * @package Woo_PicPay/Admin/Notices
 */

if(!defined('ABSPATH')) {
	exit;
}
?>

<div class="error inline">
	<p><strong><?php _e('PicPay Disabled', 'woo-picpay'); ?></strong>: <?php printf(__('Currency <code>%s</code> is not supported. Works only with Brazilian Real.', 'woo-picpay'), get_woocommerce_currency()); ?>
	</p>
</div>
