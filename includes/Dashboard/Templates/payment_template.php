<?php
/**
 * This file is a template.
 *
 * @author     Nicola Palermo
 * @since      0.1.0
 * @package    Ear2Words\Dashboar\Templates
 */

/**
 * This is a template.
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
	<h1><?php esc_html_e( 'Select Plan', 'ear2words' ); ?></h1>
	<a href="http://wordpress01.local/wp-admin/admin.php?page=ear2words_settings&payment=true" >prova success</a>
	<a href="http://wordpress01.local/wp-admin/admin.php?page=ear2words_settings&payment=false" >prova cancel</a>
	<form method="POST" id="form">
		<select name="pricing_plan" id="select">
			<option value="plan_H6i0TeOPhpY6DN">Premium</option>
			<option value="plan_H6KKmWETz5hkCu">Standard</option>
		</select>
		<input type="submit" value="Submit">
	</form>

	<?php // phpcs:disable ?>
	<script>
		const WP_GLOBALS = {
			adminAjax: "<?php echo esc_html( admin_url( 'admin-ajax.php' ) ); ?>",
			nonce: "<?php echo esc_js( wp_create_nonce( 'itr_ajax_nonce' ) ); ?>"
		}
	</script>
	<script src="https://js.stripe.com/v3/"></script>
	<script src="<?php echo esc_url(EAR2WORDS_URL . 'src/payment/payment_template.js'); ?>"></script>
	<?php // phpcs:enable ?>
</body>
</html>
