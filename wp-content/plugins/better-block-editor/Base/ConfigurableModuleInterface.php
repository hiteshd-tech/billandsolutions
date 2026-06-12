<?php
/**
 * Interface for modules that need to display their own settings.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Base;

defined( 'ABSPATH' ) || exit;

interface ConfigurableModuleInterface {
	/**
	 * Return settings definitions for this module.
	 *
	 * @return array[]
	 */
	public static function get_settings(): array;
}
