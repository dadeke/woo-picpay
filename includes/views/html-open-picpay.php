<?php
/**
 * Open PicPay template
 *
 * @package Woo_PicPay/Templates
 */
if(!defined('ABSPATH')) {
	exit;
}
?>

<a class="button alt" href="<?php echo esc_url($payment_url); ?>" target="_blank"><?php esc_html_e('Open PicPay', 'woo-picpay'); ?></a>
