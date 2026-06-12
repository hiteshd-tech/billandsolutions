<?php
/**
 * The7 dashboard registration form.
 *
 * @package The7
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="the7-registration-section">
	<h3><?php echo esc_html_x( 'Thank You for Choosing The7', 'admin', 'the7mk2' ); ?></h3>
	<p>
		<?php
		echo wp_kses_post(
			sprintf(
				/* translators: %s: themepurchase link. */
				_x(
					'Please register this copy of the theme to get access to premium plugins, pre-made websites, 1-click updates and more. If you don\'t have a license yet, you can purchase it %s.',
					'admin',
					'the7mk2'
				),
				'<a href="https://themeforest.net/checkout/from_item/5556590?license=regular&amp;support=bundle_6month&amp;ref=Dream-Theme" target="_blank">' . esc_html_x( 'here', 'admin, theme purchase link text', 'the7mk2' ) . '</a>'
			)
		);
		?>
	</p>
</div>
<div class="the7-divider"></div>
<div class="the7-registration-section">
	<h3><?php echo esc_html( _x( 'Theme Registration', 'admin', 'the7mk2' ) ); ?></h3>
	<form method="post">
		<?php settings_fields( 'the7_theme_registration' ); ?>

		<p>
			<?php echo esc_html_x( 'Purchase Code:', 'admin', 'the7mk2' ); ?><br>
			<input id="the7_purchase_code" class="of-input" name="the7_purchase_code" type="text" value="" size="36" title="<?php echo esc_attr_x( 'Purchase Code', 'admin', 'the7mk2' ); ?>">
		</p>
		<p>
			<label>
				<input type="checkbox" id="the7-registration-terms">&nbsp;
				<?php
				echo wp_kses_post(
					sprintf(
						_x(
							/* translators: %s: license link. */
							'I give my consent to record my site address and purchase code to ensure %s and copyright compliance. I understand that this information will be stored for as long as the purchase code remains valid.',
							'admin',
							'the7mk2'
						),
						'<a href="' . esc_url( The7_Remote_API::LICENSE_URL ) . '" target="_blank">license</a>'
					)
				);
				?>
			</label>
		</p>
		<p>
			<button type="submit" id="the7-register-theme-button" class="button button-primary" name="register_theme" value="register" title="<?php echo esc_attr_x( 'Register Theme', 'admin', 'the7mk2' ); ?>" disabled="disabled"><?php echo esc_html_x( 'Register Theme', 'admin', 'the7mk2' ); ?></button>
		</p>
	</form>
</div>
