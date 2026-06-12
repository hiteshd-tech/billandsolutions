<?php
/**
 * Module for adding responsive styles to buttons
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\ButtonsResponsive;

use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Base\ResponsiveBlockModuleBase;
use BetterBlockEditor\Core\BlockUtils;

defined( 'ABSPATH' ) || exit;

class Module extends ResponsiveBlockModuleBase implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'buttons-responsive';
	const ASSETS_BUILD_PATH = 'editor/blocks/buttons/responsiveness/';

	const SETTINGS_ORDER = 650;

	const BLOCK_NAME = 'core/buttons';

	public static function get_title() {
		return __( 'Responsive Buttons', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'Add Responsive Settings to Buttons block.', 'better-block-editor' );
	}

	protected function need_to_apply_changes( $block_content, $block, $wp_block_instance ) {
		return true;
	}

	protected function render( $block_content, $block, $wp_block_instance ) {
		$class_id      = BlockUtils::get_unique_class_id( $block_content );
		$block_content = BlockUtils::append_classes( $block_content, array( $class_id ) );

		$orientation   = $this->get_responsive_setting( 'orientation', 'row' );
		$justification = $this->get_responsive_setting( 'justification', 'left' );

		$horizontal_alignment_property = in_array( $orientation, array( 'row', 'row-reverse' ), true )
			? 'justify-content'
			: 'align-items';

		$horizontal_alignment_value = BlockUtils::get_horizontal_alignment_by_attribute(
			$justification,
			$orientation === 'row-reverse'
		);

		$css_rules = array(
			'flex-direction'               => $orientation,
			$horizontal_alignment_property => $horizontal_alignment_value,
		);

		BlockUtils::add_style_for_media_query(
			"@media screen and (width <= {$this->switch_width})",
			".wp-block-buttons.{$class_id}.{$class_id}.{$class_id}",
			$css_rules
		);

		return $block_content;
	}
}
