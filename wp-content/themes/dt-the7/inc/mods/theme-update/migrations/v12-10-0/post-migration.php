<?php
/**
 * Migrate post content using Better Block Editor routines.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v12_10_0;

use The7\Mods\Theme_Update\Base\Block_Editor\Post_Content_Wp_Block_Processor;
use The7\Mods\Theme_Update\Base\Block_Editor\Post_Migration_Base;

defined( 'ABSPATH' ) || exit;

final class Post_Migration extends Post_Migration_Base {

	private const CONTENT_MIGRATIONS_NAMESPACE = __NAMESPACE__ . '\\Post_Migrations';
	private const MIGRATION_SEQUENCE           = [
		'Visibility_Visible',
		'Buttons_Responsive',
		'Columns_Responsive',
		'Grid_Cleanup',
		'Grid_Migration',
		'Post_Template_Responsive',
		'Row_Cleanup',
		'Row_Migration',
		'Text_Responsive',
		'Group_Cleanup',
		'Block_Name_Prefix',
		'Attribute_Group_Prefix',
		'Css_Class_Name',
	];

	protected static function process_post( $post ) {
		$migration_classes = array_map(
			function ( $class ) {
				return self::CONTENT_MIGRATIONS_NAMESPACE . '\\' . $class;
			},
			self::MIGRATION_SEQUENCE
		);

		$processor          = new Post_Content_Wp_Block_Processor( $migration_classes );
		$post->post_content = $processor->process( $post->post_content );

		return $post;
	}
}
