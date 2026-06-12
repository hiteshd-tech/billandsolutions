<?php
/**
 * Portfolio admin part.
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Presscore_Mod_Portfolio_Admin {

	public function add_meta_boxes( $metaboxes ) {
		$metaboxes[] = plugin_dir_path( __FILE__ ) . 'metaboxes/metaboxes-portfolio.php';
		return $metaboxes;
	}

	public function add_basic_meta_boxes_support( $pages ) {
		$pages[] = \The7_Core\Mods\Post_Type_Builder\Bundled\Portfolio_Post_Type::get_name();
		return $pages;
	}

	public function add_options( $options ) {
		if ( array_key_exists( 'of-blog-and-portfolio-menu', $options ) ) {
			if ( defined( 'PRESSCORE_STYLESHEETS_VERSION' ) && version_compare( PRESSCORE_STYLESHEETS_VERSION, '6.2.0', '>=' ) ) {
				$options_file = 'options/options-portfolio.php';
			} else {
				$options_file = 'options/options-portfolio-old.php';
			}
			$options['of-portfolio-mod-injected-options'] = plugin_dir_path( __FILE__ ) . $options_file;
			$options['of-portfolio-mod-injected-slug-options'] = plugin_dir_path( __FILE__ ) . 'options/options-slug-portfolio.php';
		}
		if ( function_exists( 'presscore_module_archive_get_menu_slug' ) && array_key_exists( presscore_module_archive_get_menu_slug(), $options ) ) {
			$options['of-portfolio-mod-injected-archive-options'] = plugin_dir_path( __FILE__ ) . 'options/options-archive-portfolio.php';
		}
		return $options;
	}

	public function js_composer_default_editor_post_types_filter( $post_types ) {
		$post_types[] = \The7_Core\Mods\Post_Type_Builder\Bundled\Portfolio_Post_Type::get_name();
		return $post_types;
	}

	/**
	 * Add menu item, linked to page with import by url interface.
	 *
	 * @since 14.0.0
	 *
	 * @param array $menu_items
	 *
	 * @return array
	 */
	public function add_import_by_url_menu_item( $menu_items ) {
		$menu_items[] = array(
			'edit.php?post_type=dt_portfolio',
			_x( 'Import Project', 'backend portfolio', 'dt-the7-core' ),
			_x( 'Import', 'backend portfolio', 'dt-the7-core' ),
			'the7-import-dt_portfolio-by-url',
			'post-new.php?post_type=dt_portfolio',
		);

		return $menu_items;
	}

	/**
	 * Add Bulk edit fields.
	 */
	function bulk_edit_custom_box( $col, $type ) {
		// display for one column
		if ( $col !== 'presscore-sidebar' || $type !== \The7_Core\Mods\Post_Type_Builder\Bundled\Portfolio_Post_Type::get_name() ) {
			return;
		}

		$no_change_option = '<option value="-1">' . _x( '&mdash; No Change &mdash;', 'backend bulk edit', 'dt-the7-core' ) .'</option>';
		?>
		<div class="clear"></div>
		<div class="presscore-bulk-actions">
			<fieldset class="inline-edit-col-left dt-inline-edit-sidebars">
				<legend class="inline-edit-legend"><?php _ex( 'Project options', 'backend bulk edit', 'dt-the7-core' ); ?></legend>
				<div class="inline-edit-col">
					<label class="alignleft">
						<span class="title"><?php _ex( 'Project link', 'backend bulk edit', 'dt-the7-core' ); ?></span>
						<select name="_dt_bulk_edit_project_show_link">
							<?php echo $no_change_option; ?>
							<option value="0"><?php _ex( 'Disabled', 'backend bulk edit', 'dt-the7-core' ); ?></option>
							<option value="2"><?php _ex( 'Link in projects lists only', 'backend bulk edit', 'dt-the7-core' ); ?></option>
							<option value="1"><?php _ex( 'Link in projects lists and on project page', 'backend bulk edit', 'dt-the7-core' ); ?></option>
						</select>
					</label>
				</div>
			</fieldset>
		</div>
		<?php
	}

	/**
	 * Save changes made by bulk edit.
	 */
	function bulk_edit_save( $post_ID, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_REQUEST['_ajax_nonce'] ) && ! isset( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if ( ! check_ajax_referer( 'bulk-posts', false, false ) ) {
			return;
		}

		// Check permissions
		if ( ! current_user_can( 'edit_page', $post_ID ) ) {
			return;
		}

		if ( isset( $_REQUEST['bulk_edit'] ) ) {
			if ( isset( $_REQUEST['_dt_bulk_edit_project_show_link'] ) && in_array( $_REQUEST['_dt_bulk_edit_project_show_link'], array( '0', '1', '2' ), true ) ) {
				update_post_meta(
					$post_ID,
					'_dt_project_options_show_link',
					$_REQUEST['_dt_bulk_edit_project_show_link']
				);
			}
		}
	}
}
