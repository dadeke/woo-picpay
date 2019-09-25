<?php
/**
 * Admin help message.
 *
 * @package Woo_PicPay/Admin/Settings
 */

if(!defined('ABSPATH')) {
	exit;
}

if(apply_filters('woo_picpay_help_message', true)) : ?>
	<div class="updated inline woocommerce-message">
		<p><?php echo esc_html(sprintf(__('Help us keep the %s plugin free making a donation or rate %s on WordPress.org. Thank you in advance!', 'woo-picpay'), __('QR Code PicPay for WooCommerce', 'woo-picpay'), '&#9733;&#9733;&#9733;&#9733;&#9733;')); ?></p>
		<p><a href="https://app.picpay.com/user/deividsondamasio" target="_blank" class="button button-primary"><?php esc_html_e('Make a donation', 'woo-picpay'); ?></a> <a href="https://wordpress.org/support/view/plugin-reviews/woo-picpay?filter=5#postform" target="_blank" class="button button-secondary"><?php esc_html_e('Make a review', 'woo-picpay'); ?></a></p>
	</div>
<?php endif;
