<?php
/**
 * Bullets switch migration for carousel widgets.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v10_5_0;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;

defined( 'ABSPATH' ) || exit;

/**
 * Bullets_Switch_Migration class.
 */
class Bullets_Switch_Migration extends Widget_Migration {

	/**
	 * Default widget migration logic here.
	 *
	 * @see Widget_Migration::migrate()
	 */
	public function do_apply() {
		$settings = [
			'show_bullets_tablet',
			'show_bullets_mobile',
		];

		$bullets_is_hidden = ( $this->get( 'show_bullets' ) === '' );

		if ( $bullets_is_hidden ) {
			$this->set( 'show_bullets', 'n' );
		}

		foreach ( $settings as $setting ) {
			if ( $this->get( $setting ) ) {
				continue;
			}

			if ( $bullets_is_hidden ) {
				$this->remove( $setting );
			} else {
				$this->set( $setting, 'n' );
			}
		}
	}

	/**
	 * List of widgets to apply migration.
	 *
	 * @return \string[][]
	 */
	public static function get_callback_args_array() {
		return [
			[ 'the7_content_carousel' ],
			[ 'the7-wc-products-carousel' ],
			[ 'the7_elements_carousel' ],
			[ 'the7-elements-simple-posts-carousel' ],
			[ 'the7-simple-product-categories-carousel' ],
			[ 'the7-elements-woo-simple-products-carousel' ],
			[ 'the7_testimonials_carousel' ],
		];
	}

}
