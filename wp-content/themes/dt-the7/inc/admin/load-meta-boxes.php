<?php
/**
 * Load Meta boxes
 *
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

require_once PRESSCORE_EXTENSIONS_DIR . '/meta-box.php';

/**
 * @return void
 */
function presscore_load_meta_boxes() {
	$metaboxes = [];

	require_once __DIR__ . '/meta-boxes/metabox-fields-templates.php';
	require_once __DIR__ . '/meta-boxes/metaboxes-init.php';

	if ( ! the7_is_elementor_theme_mode_active() ) {
		require_once __DIR__ . '/meta-boxes/taxonomy-meta-box.php';

		$dir       = plugin_dir_path( __FILE__ );
		$metaboxes = [
			"{$dir}meta-boxes/metaboxes.php",
			"{$dir}meta-boxes/metaboxes-blog.php",
			"{$dir}meta-boxes/metaboxes-microsite.php",
		];
	}

	$metaboxes = apply_filters( 'presscore_load_meta_boxes', $metaboxes );

	foreach ( $metaboxes as $file ) {
		include_once $file;
	}
}
add_action( 'admin_init', 'presscore_load_meta_boxes', 20 );
