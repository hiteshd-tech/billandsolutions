<?php
/**
 * Demo Factory.
 *
 * Factory for creating Demo instances.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent\Demo;

use BetterBlockEditor\Modules\DemoContent\RemoteAPI\Demo as RemoteApiDemo;
use BetterBlockEditor\Modules\DemoContent\Demo\Demo;

defined( 'ABSPATH' ) || exit;

/**
 * Class Factory
 *
 * Produces Demo objects from provided data.
 */
class Factory {

	/**
	 * Create a Demo instance from provided data.
	 *
	 * @param mixed $demo_id Demo identifier.
	 * @return Demo
	 */
	public static function create( $demo_id ) {
		$demos = RemoteApiDemo::get_demos();
		$demo  = $demos[ $demo_id ] ?? array();

		return new Demo( $demo );
	}
}
