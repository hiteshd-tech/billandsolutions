<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once __DIR__ . '/class-the7-demo-content.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function the7_demo_content() {
	static $instance = null;

	if ( null === $instance ) {
		$instance = new The7_Demo_Content();
	}

	return $instance;
}

the7_demo_content();

/**
 * @return array
 */
function the7_demo_get_quick_search_tags_list() {
	$tags                   = [
		'fse'       => 'Block Editor (FSE)',
		'elementor' => 'Elementor',
		'wpbakery'  => 'WPBakery',
		'store'     => 'Shop',
	];
	$demo_has_been_imported = false;
	foreach ( the7_demo_content()->get_demos() as $demo ) {
		if ( $demo->partially_imported() ) {
			$demo_has_been_imported = true;
		}
	}
	if ( $demo_has_been_imported ) {
		$tags['imported'] = 'Imported';
	}

	return $tags;
}

/**
 * Prevent default WC pages creation during demo install.
 *
 * @param array $pages WC pages definition array.
 *
 * @return array
 */
function the7_demo_prevent_default_wc_pages_creation( $pages ) {
	if ( isset( $_GET['page'], $_POST['action'], $_POST['context'] ) && $_GET['page'] === 'the7-plugins' && $_POST['action'] === 'tgmpa-bulk-activate' && $_POST['context'] === 'demo_install' ) {
		return [];
	}

	return $pages;
}

add_filter( 'woocommerce_create_pages', 'the7_demo_prevent_default_wc_pages_creation' );
