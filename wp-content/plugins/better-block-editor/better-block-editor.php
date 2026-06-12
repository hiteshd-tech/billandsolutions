<?php
/**
 * Plugin Name:       Better Block Editor (BBE)
 * Description:       Better Block Editor (BBE) adds responsive layout controls, hover effects, on-scroll animations, and ready-to-use site templates to Block Editor.
 * Requires at least: 6.8
 * Requires PHP:      7.4
 * Version:           1.4.1
 * Author:            Dream-Theme
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       better-block-editor
 *
 * @package           BetterBlockEditor
 */

use BetterBlockEditor\Plugin;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/plugin.php';

define( 'WPBBE_VERSION', '1.4.1' );

define( 'WPBBE_FILE', __FILE__ );
define( 'WPBBE_DIR', plugin_dir_path( WPBBE_FILE ) );
define( 'WPBBE_BASE', plugin_basename( WPBBE_FILE ) );
define( 'WPBBE_URL', plugins_url( '/', WPBBE_FILE ) );
define( 'WPBBE_URL_DIST', WPBBE_URL . 'dist/' );
define( 'WPBBE_DIST', WPBBE_DIR . 'dist/' );
define( 'WPBBE_ULR_LIB_ASSETS', WPBBE_URL . 'lib/' );
define( 'WPBBE_BLOCKS_DIR', WPBBE_DIST . 'blocks/' );
define( 'WPBBE_TEMPLATES_DIR', WPBBE_DIR . 'templates/' );
define( 'WPBBE_PLUGIN_ID', 'better-block-editor' );
define( 'WPBBE_PLUGIN_NAME', 'Better Block Editor' );
define( 'WPBBE_REST_BASE', 'wpbbe/v1' );
// register uninstall hook inside activate hook
// it has to be on this stage
register_activation_hook( __FILE__, 'wpbbe_plugin_activate' );

function wpbbe_plugin_activate() {
	register_uninstall_hook( __FILE__, array( 'BetterBlockEditor\Plugin', 'on_uninstall' ) );
}

Plugin::instance();
