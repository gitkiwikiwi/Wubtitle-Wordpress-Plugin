<?php
/**
 * This file is a template.
 *
 * @author     Alessio Catania
 * @since      0.1.0
 * @package    Wubtitle\Dashboard\Templates
 */

/**
 * This template displays the update plan page.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Payment</title>
</head>
<body>
	<div class="warning" id="error-message" style="color:red; text-align:center"></div>
	<?php // phpcs:disable ?>
	<script>
		const WP_GLOBALS = {
			adminAjax: "<?php echo esc_html( admin_url( 'admin-ajax.php' ) ); ?>",
			nonce: "<?php echo esc_js( wp_create_nonce( 'itr_ajax_nonce' ) ); ?>",
			wubtitleEnv: "<?php echo defined( 'WP_WUBTITLE_ENV' ) ? WP_WUBTITLE_ENV : ''; ?>"
		}
	</script>
	<script src="https://js.stripe.com/v3/"></script>
	<script src="<?php echo esc_url(WUBTITLE_URL . 'assets/payment/update_template.js'); ?>"></script>
	<?php // phpcs:enable ?>
</body>
</html>
