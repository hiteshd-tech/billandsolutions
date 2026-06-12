<?php
/**
 * Adds custom text formatting options to blocks.
 *
 * @package BbeProKit
 */

namespace BbeProKit\Modules\Format;

use BbeProKit\Base\ModuleBasePro;
use BetterBlockEditor\Base\ManagableModuleInterface;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBasePro implements ManagableModuleInterface {

	const ASSETS_BUILD_PATH = 'editor/formatting/';
	const MODULE_IDENTIFIER = 'formatting';

	const SETTINGS_ORDER = 1050;

	public static function get_title() {
		return __( 'Block Formatting', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'Add custom formatting to blocks', 'better-block-editor' );
	}
}
