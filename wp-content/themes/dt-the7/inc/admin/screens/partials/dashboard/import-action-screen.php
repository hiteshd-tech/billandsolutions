<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

/**
 * @var string $starting_text
 * @var bool $require_elementor_pro
 * @var string $admin_page_url
 * @var string $error
 */
?>
<div id="the7-dashboard" class="wrap">
	<h1><?php esc_html_e( 'Pre-made Website Templates', 'the7mk2' ); ?></h1>
	<div class="wp-header-end"></div>

	<?php if ( empty( $error ) ) : ?>

	<p><?php echo wp_kses_post( $starting_text ); ?></p>

	<?php if ( $require_elementor_pro ) : ?>

	<div id="elementor-pro-notice" class="notice notice-info inline the7-notice the7-dashboard-notice the7-debug-notice">
		<p role="heading"><?php esc_html_e( 'Elementor Pro or PRO Elements required', 'the7mk2' ); ?></p>
		<p>
			<?php
			esc_html_e(
				'This pre-made website template requires Elementor Pro (premium) or PRO Elements (free) to function properly. Neither plugin was detected in your system.',
				'the7mk2'
			);
			?>
		</p>
		<p>
			<a href="https://elementor.com/pro/" id="buy-elementor" target="_blank" class="button" rel="nofollow"><?php esc_html_e( 'Retry and buy Elementor Pro', 'the7mk2' ); ?></a>
			&nbsp;&nbsp;&nbsp;
			<a href="#" class="button-primary"><?php esc_html_e( 'Install PRO Elements and continue', 'the7mk2' ); ?></a>
		</p>
	</div>

	<?php endif; ?>

	<div class="feature-section">
		<div class="the7-import-feedback"></div>
		<div class="the7-go-back-link hide-if-js">
			<p>
				<?php echo esc_html_x( 'All done.', 'admin', 'the7mk2' ); ?>
			</p>
			<p>
				<?php
				echo '<a id="the7-demo-visit-site-link" href="' . esc_url( home_url() ) . '">' . esc_html_x(
					'Visit site',
					'admin',
					'the7mk2'
				) . '</a>';
				echo ' | ';
				echo '<a href="' . esc_url( $admin_page_url ) . '">' . esc_html_x(
					'Back to The7 Dashboard',
					'admin',
					'the7mk2'
				) . '</a>';
				?>
			</p>
		</div>
	</div>

	<?php else : ?>

		<?php echo wp_kses_post( $error ); ?>

	<?php endif; ?>
</div>
