<?php
/**
 * The7 dashboard de-registration form.
 *
 * @package The7
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="the7-registration-section">
	<h3><?php echo esc_html_x( 'Your copy of The7 is registered', 'admin', 'the7mk2' ); ?></h3>
	<p><?php echo esc_html_x( 'Your copy of the theme is registered and ready to use. Thanks for being part of the ever-growing The7 family.', 'admin', 'the7mk2' ); ?></p>
</div>
<div class="the7-divider"></div>
<div class="the7-registration-section">
	<h3><?php echo esc_html_x( 'Theme Registration', 'admin', 'the7mk2' ); ?></h3>
	<form method="post">
		<?php settings_fields( 'the7_theme_registration' ); ?>
		<p>
			<?php echo esc_html_x( 'Your purchase code is:', 'admin', 'the7mk2' ); ?><br>
			<code class="the7-code"><?php echo esc_html( presscore_get_censored_purchase_code() ); ?></code>
		</p>
		<p>
			<button type="submit" class="button button-primary" name="deregister_theme" value="de-register" title="<?php echo esc_attr_x( 'De-register Theme', 'admin', 'the7mk2' ); ?>"><?php echo esc_html_x( 'De-register Theme', 'admin', 'the7mk2' ); ?></button>
		</p>
	</form>
</div>
