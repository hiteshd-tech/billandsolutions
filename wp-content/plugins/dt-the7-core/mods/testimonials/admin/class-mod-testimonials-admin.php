<?php
/**
 * Testimonials admin part.
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Presscore_Mod_Testimonials_Admin {

	public function add_meta_boxes( $metaboxes ) {
		$metaboxes[] = plugin_dir_path( __FILE__ ) . 'metaboxes/metaboxes-testimonials.php';

		return $metaboxes;
	}

	public function add_basic_meta_boxes_support( $pages ) {
		$pages[] = \The7_Core\Mods\Post_Type_Builder\Bundled\Testimonials_Post_Type::get_name();

		return $pages;
	}

	public function render_bulk_actions( $col, $type ) {
		if ( $type !== \The7_Core\Mods\Post_Type_Builder\Bundled\Testimonials_Post_Type::get_name() || $col !== 'presscore-thumbs' ) {
			return;
		}

		$no_change_option = '<option value="-1">' . _x( '&mdash; No Change &mdash;', 'backend bulk edit', 'dt-the7-core' ) . '</option>';
		?>
        <div class="clear"></div>
        <div class="presscore-bulk-actions">
            <fieldset class="inline-edit-col-left">
                <legend class="inline-edit-legend"><?php _ex( 'Link options', 'backend bulk edit', 'dt-the7-core' ); ?></legend>
                <div class="inline-edit-col">
                    <div class="inline-edit-group">
                        <label class="alignleft">
                            <span class="title"><?php _ex( 'Link to page', 'backend bulk edit', 'dt-the7-core' ) ?></span>
							<?php
							$show_wf = array(
								1 => _x( 'Yes', 'backend bulk edit footer', 'dt-the7-core' ),
								0 => _x( 'No', 'backend bulk edit footer', 'dt-the7-core' ),
							);
							?>
                            <select name="_dt_bulk_edit_go_to_single">
								<?php echo $no_change_option ?>
								<?php foreach ( $show_wf as $value => $title ): ?>
                                    <option value="<?php echo $value ?>"><?php echo $title ?></option>
								<?php endforeach ?>
                            </select>
                        </label>
                    </div>
                </div>
            </fieldset>
        </div>
		<?php
	}

	function handle_bulk_actions( $post_ID, $post ) {
		if ( $post->post_type !== \The7_Core\Mods\Post_Type_Builder\Bundled\Testimonials_Post_Type::get_name() ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times.
		if ( ! check_ajax_referer( 'bulk-posts', false, false ) ) {
			return;
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_page', $post_ID ) ) {
			return;
		}

		if ( isset( $_REQUEST['_dt_bulk_edit_go_to_single'] ) && $_REQUEST['_dt_bulk_edit_go_to_single'] !== '-1' ) {
			update_post_meta( $post_ID, '_dt_testimonial_options_go_to_single', (int) $_REQUEST['_dt_bulk_edit_go_to_single'] );
		}
	}

}
