<?php
/**
 * Apply some css to editor blocks
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\EditorInterface;

use BetterBlockEditor\Base\ModuleBase;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase {

	const MODULE_IDENTIFIER = 'core-editor-interface-plugin';
	const ASSETS_BUILD_PATH = 'editor/plugins/interface/';

	const IS_CORE_MODULE = true;
}
