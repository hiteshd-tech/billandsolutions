<?php
/**
 * Base class for better block editor migrations within The7.
 */

namespace The7\Mods\Theme_Update\Base;

defined( 'ABSPATH' ) || exit;

abstract class Migration_Base {

	abstract public static function migrate(): bool;
}
