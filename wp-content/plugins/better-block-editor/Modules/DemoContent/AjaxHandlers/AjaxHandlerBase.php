<?php
/**
 * Base AJAX handler for DemoContent module.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent\AjaxHandlers;

defined( 'ABSPATH' ) || exit;

/**
 * Defines the contract for demo content AJAX handlers.
 */
abstract class AjaxHandlerBase {

	/**
	 * Registers all hooks required by the handler.
	 *
	 * @return void
	 */
	abstract public static function register();
}
