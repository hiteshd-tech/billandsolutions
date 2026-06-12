<?php
/**
 * Theme init.
 *
 * @since 1.0.0
 *
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

define( 'THE7_MINIMUM_COMPATIBLE_WP_VERSION', '5.4.0' );

if ( ! defined( 'WPBBE_DISABLE_DEMO_CONTENT' ) ) {
	define( 'WPBBE_DISABLE_DEMO_CONTENT', true );
}

// Include an unmodified $wp_version.
require ABSPATH . WPINC . '/version.php';

/**
 * WP version.
 *
 * @var string $wp_version
 */
if ( version_compare( $wp_version, THE7_MINIMUM_COMPATIBLE_WP_VERSION, '<' ) ) {
	/**
	 * Display notice about incompatible WP version.
	 *
	 * @since 7.5.0
	 */
	add_action(
		'admin_notices',
		function () {
			?>
			<div class="notice notice-error">
				<p>
					<strong><?php echo esc_html_x( 'The7 detected incompatible WordPress version!', 'admin', 'the7mk2' ); ?></strong>
				</p>
				<p>
					<?php
					echo esc_html(
						sprintf(
							// translators: %s: Minimum WP version.
							_x( 'Minimum compatible version of WordPress is %s. Please, update your WordPress installation to be able to use The7 theme.', 'admin', 'the7mk2' ),
							THE7_MINIMUM_COMPATIBLE_WP_VERSION
						)
					);
					?>
				</p>
				<p>
					<a href="<?php echo esc_url( admin_url( '/update-core.php' ) ); ?>"><?php echo esc_html_x( 'Update WordPress.', 'admin', 'the7mk2' ); ?></a>
				</p>
			</div>
			<?php
		}
	);

	return;
}

require_once trailingslashit( get_template_directory() ) . 'inc/constants.php';

require_once PRESSCORE_DIR . '/class-the7-autoloader.php';
new The7_Autoloader();

require_once PRESSCORE_DIR . '/deprecated-functions.php';
require_once PRESSCORE_EXTENSIONS_DIR . '/core-functions.php';
require_once PRESSCORE_EXTENSIONS_DIR . '/stylesheet-functions.php';

if ( ! the7_is_gutenberg_theme_mode_active() ) {
	require_once PRESSCORE_EXTENSIONS_DIR . '/dt-pagination.php';
	require_once PRESSCORE_EXTENSIONS_DIR . '/less-vars/less-functions.php';
	require_once PRESSCORE_EXTENSIONS_DIR . '/options-framework/options-framework.php';
}

require_once PRESSCORE_CLASSES_DIR . '/presscore-config.class.php';
require_once PRESSCORE_CLASSES_DIR . '/class-presscore-post-type-rewrite-rules-filter.php';
require_once PRESSCORE_DIR . '/helpers.php';

if ( ! the7_is_gutenberg_theme_mode_active() ) {
	require_once locate_template( 'inc/widgets/load-widgets.php' );
}

require_once locate_template( 'inc/shortcodes/load-shortcodes.php' );

The7_Admin_Dashboard_Settings::init();
require_once PRESSCORE_DIR . '/theme-setup.php';

if ( ! the7_is_gutenberg_theme_mode_active() ) {
	require_once PRESSCORE_DIR . '/template-hooks.php';
	require_once PRESSCORE_DIR . '/dynamic-stylesheets-functions.php';
	require_once PRESSCORE_DIR . '/static.php';
}

require_once PRESSCORE_MODS_DIR . '/legacy/legacy.php';

require_once PRESSCORE_MODS_DIR . '/dev-tools/main-module.class.php';
The7_DevToolMainModule::init();

$critical_alert_email = The7_Admin_Dashboard_Settings::get( 'critical-alerts-email' );
if ( ! $critical_alert_email ) {
	$critical_alert_email = get_site_option( 'admin_email' );
}

if ( presscore_theme_is_activated() ) {
	$critical_alerts = new The7_Critical_Alerts( $critical_alert_email, new The7_Remote_API( presscore_get_purchase_code() ) );
	$critical_alerts->init();
}

// Bootstrap admin code for both wp-admin and WP-CLI,
// as WP-CLI does not satisfy is_admin() but still relies on the same infrastructure.
if ( ( defined( 'WP_CLI' ) && WP_CLI ) || is_admin() ) {
	require_once PRESSCORE_ADMIN_DIR . '/admin-notices.php';

	if ( ! the7_is_gutenberg_theme_mode_active() ) {
		require_once PRESSCORE_ADMIN_DIR . '/admin-functions.php';
		require_once PRESSCORE_ADMIN_DIR . '/admin-bulk-actions.php';
		require_once locate_template( 'inc/admin/load-meta-boxes.php' );
	}

	require_once PRESSCORE_ADMIN_DIR . '/site-health/tests.php';
	require_once PRESSCORE_ADMIN_DIR . '/site-health/info.php';

	The7_Theme_Auto_Deactivation::init();
	$the7_admin_dashboard = new The7_Admin_Dashboard();
	$the7_admin_dashboard->init();
}
