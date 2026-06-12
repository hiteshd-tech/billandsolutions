<?php
/**
 * Bundled plugin content
 *
 * @package The7
 * @since   5.1.5
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_admin() ) {
	return;
}

$bundled_plugins = [
	new \The7\Mods\Bundled_Content\WPB(),
	new \The7\Mods\Bundled_Content\Brainstorm(),
];

foreach ( $bundled_plugins as $bundled_plugin ) {
	/**
	 * @var The7\Mods\Bundled_Content\Abstract_Bundled_Plugin $bundled_plugin
	 */
	if ( ! $bundled_plugin->is_active() ) {
		continue;
	}

	if ( presscore_theme_is_activated() ) {
		$bundled_plugin->activate_plugin();
	} else {
		$bundled_plugin->deactivate_plugin();
	}
}
