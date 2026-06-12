<?php
/**
 * Masonry gap migration for posts widget.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v10_5_0;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;

defined( 'ABSPATH' ) || exit;

/**
 * Posts_Masonry_Gap_Migration class.
 */
class Posts_Masonry_Gap_Migration extends Widget_Migration {

	/**
	 * @return string
	 */
	public static function get_widget_name() {
		return 'the7_elements';
	}

	/**
	 * Default widget migration logic here.
	 *
	 * @see Widget_Migration::migrate()
	 */
	public function do_apply() {
		if ( $this->exists( 'rows_gap' ) ) {
			return;
		}

		$gap = $this->get( 'gap_between_posts' );
		if ( ! isset( $gap['size'] ) ) {
			$gap = [
				'unit' => 'px',
				'size' => 15,
			];
		}
		$gap['size'] = 2 * (int) $gap['size'];
		$this->set( 'gap_between_posts', $gap );
		$this->set( 'rows_gap', $gap );
	}

}
