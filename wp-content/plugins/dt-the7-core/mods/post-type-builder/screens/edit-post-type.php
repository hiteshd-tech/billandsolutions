<?php
namespace The7_Core\Mods\Post_Type_Builder\Screens;

defined( 'ABSPATH' ) || exit;

use The7_Core\Mods\Post_Type_Builder\Admin_Page;
use The7_Core\Mods\Post_Type_Builder\Handlers\Post_Types_Handler;
use The7_Core\Mods\Post_Type_Builder\Handlers\Handler;
use The7_Core\Mods\Post_Type_Builder\Models\Post_Types;
use The7_Core\Mods\Post_Type_Builder\Models\Taxonomies;
use The7_Core\Mods\Post_Type_Builder\Utility\Utility;

class Edit_Post_Type {

	/**
	 * @param array|null $data
	 *
	 * @return void
	 */
	public static function render( $data = null ) {
		$tab        = $data ? 'edit' : 'new';
		$current    = $data;
		if ( $current === null ) {
			$default = Post_Types::create_item( [] );
			$current = $default->get_raw();
		}

		$is_bundled = ! empty( $current['predefined'] );

		$buttons_style = $tab;
		if ( $is_bundled ) {
			$buttons_style = 'restore';
		}
		?>

		<div class="wrap">

			<h1 class="wp-heading-inline">
			<?php
				if ( $tab === 'new' ) {
					esc_html_e( 'New Post Type', 'dt-the7-core' );
				} else {
					esc_html_e( 'Edit Post Type', 'dt-the7-core' );
				}
			?>
			</h1>
			<a href="<?php echo esc_url( Admin_Page::get_link() ) ?>" class="page-title-action"><?php esc_html_e( 'Items list', 'dt-the7-core' ) ?></a>

			<hr class="wp-header-end">

			<?php
			$ui = new \The7_Core\Mods\Post_Type_Builder\Utility\UI();
			?>

			<form class="the7-post-types-ui" method="post" action="">
				<div class="postbox-container">
					<div id="poststuff">

						<div class="the7-section postbox">
							<div class="postbox-header">
								<h2 class="hndle ui-sortable-handle">
									<span><?php esc_html_e( 'Basic settings', 'dt-the7-core' ); ?></span>
								</h2>
								<div class="handle-actions hide-if-no-js">
									<button type="button" class="handlediv" aria-expanded="true">
										<span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Basic settings', 'dt-the7-core' ); ?></span>
										<span class="toggle-indicator" aria-hidden="true"></span>
									</button>
								</div>
							</div>
							<div class="inside">
								<div class="main">
									<table class="form-table the7-table">
										<?php
										echo $ui->get_tr_start() . $ui->get_th_start();
										echo $ui->get_label( 'name', esc_html__( 'Post Type Slug', 'dt-the7-core' ) );
										echo $ui->get_required_span();

										if ( 'edit' === $tab ) {
											echo '<p id="slugchanged" class="hidemessage">' . esc_html__( 'Slug has changed', 'dt-the7-core' ) . '<span class="dashicons dashicons-warning"></span></p>';
										}
										echo '<p id="slugexists" class="hidemessage">' . esc_html__( 'Slug already exists', 'dt-the7-core' ) . '<span class="dashicons dashicons-warning"></span></p>';

										echo $ui->get_th_end() . $ui->get_td_start();

										echo $ui->get_text_input( [
											'namearray'   => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
											'name'        => 'name',
											'textvalue'   => isset( $current['name'] ) ? esc_attr( $current['name'] ) : '',
											'maxlength'   => '20',
											'helptext'    => esc_html__( 'Use only alphanumeric, latin characters and underscores in place of spaces. Used for various queries for post type content.', 'dt-the7-core' ),
											'required'    => true,
											'placeholder' => false,
											'wrap'        => false,
											'readonly'	  => $is_bundled,
										] );

										if ( 'edit' === $tab && ! $is_bundled ) {
											echo '<p><b>';
											esc_html_e( 'DO NOT EDIT the post type slug unless also planning to migrate posts. Changing the slug registers a new post type entry!', 'dt-the7-core' );
											echo '</b></p>';
										}

										echo '<p class="the7-slug-details">';
										printf( esc_html__( 'If you want to change it (including the usage of dashes in URLs), apply the new slug in %s field below.', 'dt-the7-core' ), '<a href="#rewrite_slug">"' . esc_html__( 'Custom Rewrite Slug', 'dt-the7-core' ) . '"</a>' );
										echo '</p>';

										if ( 'edit' === $tab && ! $is_bundled ) {
											echo '<div class="the7-spacer">';
											echo $ui->get_check_input( [
												'checkvalue' => Post_Types_Handler::FIELD_POST_TYPE_UPDATE,
												'checked'    => true,
												'name'       => Post_Types_Handler::FIELD_POST_TYPE_UPDATE,
												'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_UPDATE,
												'labeltext'  => esc_html__( 'Migrate posts to newly renamed post type?', 'dt-the7-core' ),
												'helptext'   => false,
												'wrap'       => false,
											] );
											echo '</div>';
										}

										echo $ui->get_td_end(); echo $ui->get_tr_end();

										echo $ui->get_text_input( [
											'namearray' => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
											'name'      => 'singular_label',
											'textvalue' => isset( $current['singular_label'] ) ? esc_attr( $current['singular_label'] ) : '',
											'labeltext' => esc_html__( 'Singular Label', 'dt-the7-core' ),
											'aftertext' => esc_html__( '(e.g. Movie)', 'dt-the7-core' ),
											'helptext'  => esc_html__( 'Used when a singular label is needed.', 'dt-the7-core' ),
											'required'  => true,
										] );

										echo $ui->get_text_input( [
											'namearray' => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
											'name'      => 'label',
											'textvalue' => isset( $current['label'] ) ? esc_attr( $current['label'] ) : '',
											'labeltext' => esc_html__( 'Plural Label', 'dt-the7-core' ),
											'aftertext' => esc_html__( '(e.g. Movies)', 'dt-the7-core' ),
											'helptext'  => esc_html__( 'Used for the post type admin menu item.', 'dt-the7-core' ),
											'required'  => true,
										] );

										echo $ui->get_tr_end();

										echo $ui->get_tr_start() . $ui->get_th_start();
										echo $ui->get_label( 'menu_position', esc_html__( 'Admin Menu Position', 'dt-the7-core' ) );
										echo $ui->get_p(
											sprintf(
												esc_html__(
													'See %s in the "menu_position" section. Range of 5-100',
													'dt-the7-core'
												),
												sprintf(
													'<a href="https://developer.wordpress.org/reference/functions/register_post_type/#menu_position" target="_blank" rel="noopener">%s</a>',
													esc_html__( 'Available options', 'dt-the7-core' )
												)
											)
										);

										echo $ui->get_th_end() . $ui->get_td_start();
										echo $ui->get_text_input( [
											'namearray' => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
											'name'      => 'menu_position',
											'textvalue' => isset( $current['menu_position'] ) ? esc_attr( $current['menu_position'] ) : '',
											'helptext'  => esc_html__( 'Position in admin menu, where the post type should appear. "Show in Menu" must be true.', 'dt-the7-core' ),
											'wrap'      => false,
										] );
										echo $ui->get_td_end() . $ui->get_tr_end();

										if ( ! $is_bundled ) {
											echo $ui->get_tr_start() . $ui->get_th_start();

											$current_menu_icon = isset( $current['menu_icon'] ) ? esc_attr( $current['menu_icon'] ) : '';
											echo $ui->get_menu_icon_preview( $current_menu_icon );
											echo $ui->get_label( 'menu_icon', esc_html__( 'Menu Icon', 'dt-the7-core' ) );
											echo $ui->get_th_end() . $ui->get_td_start();
											echo $ui->get_text_input( [
												'namearray' => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
												'name'      => 'menu_icon',
												'textvalue' => $current_menu_icon,
												'aftertext' => 'dashicons-admin-post',
												'helptext'  => sprintf( esc_html__( 'Image URL or %sDashicon class name%s to use for icon. Custom image should be 20px by 20px.', 'dt-the7-core' ), '<a href="https://developer.wordpress.org/resource/dashicons/" target="_blank" rel="noopener">', '</a>' ),
												'wrap'      => false,
											] );

											echo '<div class="the7-spacer">';

											echo $ui->get_button( [
												'id'        => 'cptui_choose_dashicon',
												'classes'   => 'dashicons-picker',
												'textvalue' => esc_attr__( 'Choose dashicon', 'dt-the7-core' ),
											] );

											echo '&nbsp';

											echo $ui->get_button( [
												'id'        => 'the7-choose-icon',
												'textvalue' => esc_attr__( 'Choose image icon', 'dt-the7-core' ),
											] );
											echo '</div>';

											echo $ui->get_td_end() . $ui->get_tr_end();


											echo $ui->get_tr_start() . $ui->get_th_start() . esc_html__( 'Supports', 'dt-the7-core' );

											echo $ui->get_p( esc_html__( 'Add support for various available post editor features on the right.', 'dt-the7-core' ) );

											echo $ui->get_p( esc_html__( 'Use the "None" option to disable all features.', 'dt-the7-core' ) );

											echo $ui->get_th_end() . $ui->get_td_start() . $ui->get_fieldset_start();

											echo $ui->get_legend_start() . esc_html__( 'Post type options', 'dt-the7-core' ) . $ui->get_legend_end();

											$title_checked = ( ! empty( $current['supports'] ) && is_array( $current['supports'] ) && in_array( 'title', $current['supports'] ) ) ? 'true' : 'false';
											if ( 'new' === $tab ) {
												$title_checked = 'true';
											}
											echo $ui->get_check_input( [
												'checkvalue' => 'title',
												'checked'    => $title_checked,
												'name'       => 'title',
												'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_SUPPORTS,
												'textvalue'  => 'title',
												'labeltext'  => esc_html__( 'Title', 'dt-the7-core' ),
												'default'    => true,
												'wrap'       => false,
											] );

											$editor_checked = ( ! empty( $current['supports'] ) && is_array( $current['supports'] ) && in_array( 'editor', $current['supports'] ) ) ? 'true' : 'false';
											if ( 'new' === $tab ) {
												$editor_checked = 'true';
											}
											echo $ui->get_check_input( [
												'checkvalue' => 'editor',
												'checked'    => $editor_checked,
												'name'       => 'editor',
												'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_SUPPORTS,
												'textvalue'  => 'editor',
												'labeltext'  => esc_html__( 'Editor', 'dt-the7-core' ),
												'default'    => true,
												'wrap'       => false,
											] );

											$thumb_checked = ( ! empty( $current['supports'] ) && is_array( $current['supports'] ) && in_array( 'thumbnail', $current['supports'] ) ) ? 'true' : 'false';
											if ( 'new' === $tab ) {
												$thumb_checked = 'true';
											}
											echo $ui->get_check_input( [
												'checkvalue' => 'thumbnail',
												'checked'    => $thumb_checked,
												'name'       => 'thumbnail',
												'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_SUPPORTS,
												'textvalue'  => 'thumbnail',
												'labeltext'  => esc_html__( 'Featured Image', 'dt-the7-core' ),
												'default'    => true,
												'wrap'       => false,
											] );

											$excerpt_checked = ( ! empty( $current['supports'] ) && is_array( $current['supports'] ) && in_array( 'excerpt', $current['supports'] ) ) ? 'true' : 'false';
											if ( 'new' === $tab ) {
												$excerpt_checked = 'true';
											}
											echo $ui->get_check_input( [
												'checkvalue' => 'excerpt',
												'checked'    => $excerpt_checked,
												'name'       => 'excerpts',
												'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_SUPPORTS,
												'textvalue'  => 'excerpt',
												'labeltext'  => esc_html__( 'Excerpt', 'dt-the7-core' ),
												'default'    => true,
												'wrap'       => false,
											] );

											echo $ui->get_check_input( [
												'checkvalue' => 'trackbacks',
												'checked'    => ( ! empty( $current['supports'] ) && is_array( $current['supports'] ) && in_array( 'trackbacks', $current['supports'] ) ) ? 'true' : 'false',
												'name'       => 'trackbacks',
												'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_SUPPORTS,
												'textvalue'  => 'trackbacks',
												'labeltext'  => esc_html__( 'Trackbacks', 'dt-the7-core' ),
												'default'    => true,
												'wrap'       => false,
											] );

											echo $ui->get_check_input( [
												'checkvalue' => 'custom-fields',
												'checked'    => ( ! empty( $current['supports'] ) && is_array( $current['supports'] ) && in_array( 'custom-fields', $current['supports'] ) ) ? 'true' : 'false',
												'name'       => 'custom-fields',
												'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_SUPPORTS,
												'textvalue'  => 'custom-fields',
												'labeltext'  => esc_html__( 'Custom Fields', 'dt-the7-core' ),
												'default'    => true,
												'wrap'       => false,
											] );

											echo $ui->get_check_input( [
												'checkvalue' => 'comments',
												'checked'    => ( ! empty( $current['supports'] ) && is_array( $current['supports'] ) && in_array( 'comments', $current['supports'] ) ) ? 'true' : 'false',
												'name'       => 'comments',
												'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_SUPPORTS,
												'textvalue'  => 'comments',
												'labeltext'  => esc_html__( 'Comments', 'dt-the7-core' ),
												'default'    => true,
												'wrap'       => false,
											] );

											echo $ui->get_check_input( [
												'checkvalue' => 'revisions',
												'checked'    => ( ! empty( $current['supports'] ) && is_array( $current['supports'] ) && in_array( 'revisions', $current['supports'] ) ) ? 'true' : 'false',
												'name'       => 'revisions',
												'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_SUPPORTS,
												'textvalue'  => 'revisions',
												'labeltext'  => esc_html__( 'Revisions', 'dt-the7-core' ),
												'default'    => true,
												'wrap'       => false,
											] );

											echo $ui->get_check_input( [
												'checkvalue' => 'author',
												'checked'    => ( ! empty( $current['supports'] ) && is_array( $current['supports'] ) && in_array( 'author', $current['supports'] ) ) ? 'true' : 'false',
												'name'       => 'author',
												'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_SUPPORTS,
												'textvalue'  => 'author',
												'labeltext'  => esc_html__( 'Author', 'dt-the7-core' ),
												'default'    => true,
												'wrap'       => false,
											] );

											echo $ui->get_check_input( [
												'checkvalue' => 'page-attributes',
												'checked'    => ( ! empty( $current['supports'] ) && is_array( $current['supports'] ) && in_array( 'page-attributes', $current['supports'] ) ) ? 'true' : 'false',
												'name'       => 'page-attributes',
												'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_SUPPORTS,
												'textvalue'  => 'page-attributes',
												'labeltext'  => esc_html__( 'Page Attributes', 'dt-the7-core' ),
												'default'    => true,
												'wrap'       => false,
											] );

											echo $ui->get_check_input( [
												'checkvalue' => 'post-formats',
												'checked'    => ( ! empty( $current['supports'] ) && is_array( $current['supports'] ) && in_array( 'post-formats', $current['supports'] ) ) ? 'true' : 'false',
												'name'       => 'post-formats',
												'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_SUPPORTS,
												'textvalue'  => 'post-formats',
												'labeltext'  => esc_html__( 'Post Formats', 'dt-the7-core' ),
												'default'    => true,
												'wrap'       => false,
											] );

											echo $ui->get_check_input( [
												'checkvalue' => 'none',
												'checked'    => ( ! empty( $current['supports'] ) && ( is_array( $current['supports'] ) && in_array( 'none', $current['supports'] ) ) ) ? 'true' : 'false',
												'name'       => 'none',
												'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_SUPPORTS,
												'textvalue'  => 'none',
												'labeltext'  => esc_html__( 'None', 'dt-the7-core' ),
												'default'    => false,
												'wrap'       => false,
											] );

											echo $ui->get_fieldset_end() . $ui->get_td_end() . $ui->get_tr_end();
										}

										echo $ui->get_tr_start() . $ui->get_th_start() . esc_html__( 'Taxonomies', 'dt-the7-core' );

										$create_new_taxonomy_link = Admin_Page::get_taxonomy_link( Admin_Page::ACTION_NEW );
										echo $ui->get_p(
											wp_kses_post(
												sprintf(
													__( 'Add support for available registered taxonomies. Or add a %s for your post type.', 'dt-the7-core' ),
													'<a href="' . esc_url( $create_new_taxonomy_link ) . '" target="_blank">' . __( 'new one', 'dt-the7-core' ) . '</a>'
												)
											)
										);

										echo $ui->get_th_end() . $ui->get_td_start() . $ui->get_fieldset_start();

										echo $ui->get_legend_start() . esc_html__( 'Taxonomy options', 'dt-the7-core' ) . $ui->get_legend_end();

										$add_taxes              = get_taxonomies( [ 'public' => true ], 'objects' );
										$taxonomies_to_ignore   = array_keys( Taxonomies::get_bundle_definition() );
										$taxonomies_to_ignore[] = 'nav_menu';
										$taxonomies_to_ignore[] = 'post_format';

										$bundled_taxonomies = $current['bundled_taxonomies'];
										if ( $is_bundled ) {
											$taxonomies_to_ignore = array_diff( $taxonomies_to_ignore, $bundled_taxonomies );
										}

										$add_taxes = array_diff_key( $add_taxes, array_fill_keys( $taxonomies_to_ignore, null ) );

										foreach ( $add_taxes as $add_tax ) {
											$tax_slug = $add_tax->name;
											$label    = $add_tax->label;
											if ( in_array( $add_tax->name, [ 'category', 'post_tag' ] ) ) {
												$label = 'Blog ' . $label;
											}

											echo $ui->get_check_input( [
												'checkvalue' => $tax_slug,
												'checked'    => in_array( $tax_slug, $current['taxonomies'], true ),
												'name'       => $tax_slug,
												'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_RELATIONS,
												'textvalue'  => $tax_slug,
												'labeltext'  => $label,
												'helptext'   => sprintf( esc_attr__( 'Adds %s support', 'dt-the7-core' ), $label ),
												'wrap'       => false,
												'disabled'	 => in_array( $tax_slug, $bundled_taxonomies, true ),
											] );
										}
										echo $ui->get_fieldset_end() . $ui->get_td_end() . $ui->get_tr_end();
										?>
									</table>
									<p class="submit">
										<?php
										Post_Types_Handler::nonce_field();

										static::render_control_buttons( $buttons_style );

										if ( ! empty( $current ) ) { ?>
											<input type="hidden" name="<?php echo esc_attr( Post_Types_Handler::FIELD_POST_TYPE_ORIGINAL ); ?>" value="<?php echo esc_attr( $current['name'] ); ?>" />
										<?php }

										// Used to check and see if we should prevent duplicate slugs. ?>
										<input type="hidden" name="<?php echo esc_attr( Post_Types_Handler::FIELD_POST_TYPE_STATUS ); ?>" value="<?php echo esc_attr( $tab ); ?>" />
									</p>
								</div>
							</div>
						</div>

						<div class="the7-section the7-settings postbox">
							<div class="postbox-header">
								<h2 class="hndle ui-sortable-handle">
									<span><?php esc_html_e( 'Settings', 'dt-the7-core' ); ?></span>
								</h2>
								<div class="handle-actions hide-if-no-js">
									<button type="button" class="handlediv" aria-expanded="true">
										<span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Basic settings', 'dt-the7-core' ); ?></span>
										<span class="toggle-indicator" aria-hidden="true"></span>
									</button>
								</div>
							</div>
							<div class="inside">
								<div class="main">
									<table class="form-table the7-table">
										<?php
										$true_false_options = [
											'1' => esc_attr__( 'True', 'dt-the7-core' ),
											'0' => esc_attr__( 'False', 'dt-the7-core' ),
										];

										echo $ui->get_switch( [
											'namearray' => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
											'name'      => 'rewrite',
											'labeltext' => esc_html__( 'Rewrite', 'dt-the7-core' ),
											'aftertext' => esc_html__( '(default: true) Whether or not WordPress should use rewrites for this post type.', 'dt-the7-core' ),
											'options'   => $true_false_options,
											'default'   => '1',
											'selected'  => $current['rewrite'],
										] );

										echo $ui->get_text_input( [
											'namearray' => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
											'name'      => 'rewrite_slug',
											'textvalue' => isset( $current['rewrite_slug'] ) ? esc_attr( $current['rewrite_slug'] ) : '',
											'labeltext' => esc_html__( 'Custom Rewrite Slug', 'dt-the7-core' ),
											'aftertext' => esc_attr__( '(default: post type slug)', 'dt-the7-core' ),
											'helptext'  => esc_html__( 'Custom post type slug to use instead of the default.', 'dt-the7-core' ),
										] );

										echo $ui->get_switch( [
											'namearray' => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
											'name'      => 'rewrite_withfront',
											'labeltext' => esc_html__( 'With Front', 'dt-the7-core' ),
											'aftertext' => esc_html__( '(default: true) Should the permalink structure be prepended with the front base. (example: if your permalink structure is /blog/, then your links will be: false->/news/, true->/blog/news/).', 'dt-the7-core' ),
											'options'   => $true_false_options,
											'default'   => '1',
											'selected'  => $current['rewrite_withfront'],
										] );

										echo $ui->get_switch( [
											'namearray' => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
											'name'      => 'exclude_from_search',
											'labeltext' => esc_html__( 'Exclude From Search', 'dt-the7-core' ),
											'aftertext' => esc_html__( '(default: false) Whether or not to exclude posts with this post type from front end search results.', 'dt-the7-core' ),
											'options'   => $true_false_options,
											'default'   => '0',
											'selected'  => $current['exclude_from_search'],
										] );

										if ( ! $is_bundled ) {
											echo $ui->get_switch( [
												'namearray' => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
												'name'      => 'publicly_queryable',
												'labeltext' => esc_html__( 'Publicly Queryable', 'dt-the7-core' ),
												'aftertext' => esc_html__( '(default: true) Whether or not queries can be performed on the front end as part of parse_request()', 'dt-the7-core' ),
												'options'   => $true_false_options,
												'default'   => '1',
												'selected'  => $current['publicly_queryable'],
											] );
										}

										echo $ui->get_switch( [
											'namearray' => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
											'name'      => 'delete_with_user',
											'labeltext' => esc_html__( 'Delete with user', 'dt-the7-core' ),
											'aftertext' => esc_html__( '(default: false) Whether to delete posts of this type when deleting a user.', 'dt-the7-core' ),
											'options'   => $true_false_options,
											'default'   => '0',
											'selected'  => $current['delete_with_user'],
										] );

										echo $ui->get_switch( [
											'namearray' => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
											'name'      => 'show_thumbnail_admin_column',
											'labeltext' => esc_html__( 'Thumbnails admin column', 'dt-the7-core' ),
											'aftertext' => esc_html__( '(default: false) Whether to show posts thumbnails admin column.', 'dt-the7-core' ),
											'options'   => $true_false_options,
											'default'   => '0',
											'selected'  => $current['show_thumbnail_admin_column'],
										] );

										echo $ui->get_tr_start() . $ui->get_th_start();
										echo $ui->get_label( 'has_archive', esc_html__( 'Has Archive', 'dt-the7-core' ) );
										echo $ui->get_p( esc_html__( 'If left blank, the archive slug will default to the post type slug.', 'dt-the7-core' ) );
										echo $ui->get_th_end() . $ui->get_td_start();

										echo $ui->get_switch( [
											'namearray' => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
											'name'      => 'has_archive',
											'aftertext' => esc_html__( '(default: false) Whether or not the post type will have a post type archive URL.', 'dt-the7-core' ),
											'options'   => $true_false_options,
											'default'   => '0',
											'selected'  => $current['has_archive'],
											'wrap'      => false,
										] );

										echo '<br/>';

										echo $ui->get_text_input( [
											'namearray'      => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
											'name'           => 'has_archive_string',
											'textvalue'      => isset( $current['has_archive_string'] ) ? esc_attr( $current['has_archive_string'] ) : '',
											'aftertext'      => esc_attr__( 'Slug to be used for archive URL.', 'dt-the7-core' ),
											'helptext_after' => true,
											'wrap'           => false,
										] );
										echo $ui->get_td_end() . $ui->get_tr_end();

										if ( ! $is_bundled ) {
											echo $ui->get_text_input( [
												'namearray' => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
												'name'      => 'capability_type',
												'textvalue' => isset( $current['capability_type'] ) ? esc_attr( $current['capability_type'] ) : 'post',
												'labeltext' => esc_html__( 'Capability Type', 'dt-the7-core' ),
												'helptext'  => esc_html__( 'The post type to use for checking read, edit, and delete capabilities. A comma-separated second value can be used for plural version.', 'dt-the7-core' ),
											] );
										}
										?>
									</table>
								</div>
							</div>
						</div>

						<p>
							<?php static::render_control_buttons( $buttons_style ) ?>
						</p>
					</div>
			</form>
		</div><!-- End .wrap -->
		<?php
	}

	protected static function render_labels_block( $ui, $current ) {
		?>

		<div class="the7-section the7-labels postbox">
			<div class="postbox-header">
				<h2 class="hndle ui-sortable-handle">
					<span><?php esc_html_e( 'Additional labels', 'dt-the7-core' ); ?></span>
				</h2>
				<div class="handle-actions hide-if-no-js">
					<button type="button" class="handlediv" aria-expanded="true">
						<span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Basic settings', 'dt-the7-core' ); ?></span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
				</div>
			</div>
			<div class="inside">
				<div class="main">
					<table class="form-table the7-table">
						<?php

						if ( isset( $current['description'] ) ) {
							$current['description'] = stripslashes_deep( $current['description'] );
						}
						echo $ui->get_textarea_input( [
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
							'name'      => 'description',
							'rows'      => '4',
							'cols'      => '40',
							'textvalue' => isset( $current['description'] ) ? esc_textarea( $current['description'] ) : '',
							'labeltext' => esc_html__( 'Post Type Description', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Perhaps describe what your custom post type is used for?', 'dt-the7-core' ),
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Menu Name', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Custom admin menu name for your custom post type.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'menu_name',
							'textvalue' => isset( $current['labels']['menu_name'] ) ? esc_attr( $current['labels']['menu_name'] ) : '',
							'aftertext' => esc_html__( '(e.g. My Movies)', 'dt-the7-core' ),
							'data'      => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'My %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'All Items', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used in the post type admin submenu.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'all_items',
							'textvalue' => isset( $current['labels']['all_items'] ) ? esc_attr( $current['labels']['all_items'] ) : '',
							'aftertext' => esc_html__( '(e.g. All Movies)', 'dt-the7-core' ),
							'data'      => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'All %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Add New', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used in the post type admin submenu.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'add_new',
							'textvalue' => isset( $current['labels']['add_new'] ) ? esc_attr( $current['labels']['add_new'] ) : '',
							'aftertext' => esc_html__( '(e.g. Add New)', 'dt-the7-core' ),
							'data'      => [
								/* translators: Used for autofill */
								'label'     => esc_attr__( 'Add new', 'dt-the7-core' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Add New Item', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used at the top of the post editor screen for a new post type post.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'add_new_item',
							'textvalue' => isset( $current['labels']['add_new_item'] ) ? esc_attr( $current['labels']['add_new_item'] ) : '',
							'aftertext' => esc_html__( '(e.g. Add New Movie)', 'dt-the7-core' ),
							'data'      => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Add new %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Edit Item', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used at the top of the post editor screen for an existing post type post.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'edit_item',
							'textvalue' => isset( $current['labels']['edit_item'] ) ? esc_attr( $current['labels']['edit_item'] ) : '',
							'aftertext' => esc_html__( '(e.g. Edit Movie)', 'dt-the7-core' ),
							'data'      => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Edit %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'New Item', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Post type label. Used in the admin menu for displaying post types.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'new_item',
							'textvalue' => isset( $current['labels']['new_item'] ) ? esc_attr( $current['labels']['new_item'] ) : '',
							'aftertext' => esc_html__( '(e.g. New Movie)', 'dt-the7-core' ),
							'data'      => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'New %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'View Item', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used in the admin bar when viewing editor screen for a published post in the post type.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'view_item',
							'textvalue' => isset( $current['labels']['view_item'] ) ? esc_attr( $current['labels']['view_item'] ) : '',
							'aftertext' => esc_html__( '(e.g. View Movie)', 'dt-the7-core' ),
							'data'      => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'View %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'View Items', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used in the admin bar when viewing editor screen for a published post in the post type.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'view_items',
							'textvalue' => isset( $current['labels']['view_items'] ) ? esc_attr( $current['labels']['view_items'] ) : '',
							'aftertext' => esc_html__( '(e.g. View Movies)', 'dt-the7-core' ),
							'data'      => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'View %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Search Item', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used as the text for the search button on post type list screen.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'search_items',
							'textvalue' => isset( $current['labels']['search_items'] ) ? esc_attr( $current['labels']['search_items'] ) : '',
							'aftertext' => esc_html__( '(e.g. Search Movies)', 'dt-the7-core' ),
							'data'      => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Search %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Not Found', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used when there are no posts to display on the post type list screen.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'not_found',
							'textvalue' => isset( $current['labels']['not_found'] ) ? esc_attr( $current['labels']['not_found'] ) : '',
							'aftertext' => esc_html__( '(e.g. No Movies found)', 'dt-the7-core' ),
							'data'      => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'No %s found', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Not Found in Trash', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used when there are no posts to display on the post type list trash screen.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'not_found_in_trash',
							'textvalue' => isset( $current['labels']['not_found_in_trash'] ) ? esc_attr( $current['labels']['not_found_in_trash'] ) : '',
							'aftertext' => esc_html__( '(e.g. No Movies found in Trash)', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'No %s found in trash', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						// As of 1.4.0, this will register into `parent_item_colon` paramter upon registration and export.
						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Parent', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used for hierarchical types that need a colon.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'parent',
							'textvalue' => isset( $current['labels']['parent'] ) ? esc_attr( $current['labels']['parent'] ) : '',
							'aftertext' => esc_html__( '(e.g. Parent Movie:)', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Parent %s:', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Featured Image', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used as the "Featured Image" phrase for the post type.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'featured_image',
							'textvalue' => isset( $current['labels']['featured_image'] ) ? esc_attr( $current['labels']['featured_image'] ) : '',
							'aftertext' => esc_html__( '(e.g. Featured image for this movie)', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Featured image for this %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Set Featured Image', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used as the "Set featured image" phrase for the post type.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'set_featured_image',
							'textvalue' => isset( $current['labels']['set_featured_image'] ) ? esc_attr( $current['labels']['set_featured_image'] ) : '',
							'aftertext' => esc_html__( '(e.g. Set featured image for this movie)', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Set featured image for this %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Remove Featured Image', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used as the "Remove featured image" phrase for the post type.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'remove_featured_image',
							'textvalue' => isset( $current['labels']['remove_featured_image'] ) ? esc_attr( $current['labels']['remove_featured_image'] ) : '',
							'aftertext' => esc_html__( '(e.g. Remove featured image for this movie)', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Remove featured image for this %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Use Featured Image', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used as the "Use as featured image" phrase for the post type.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'use_featured_image',
							'textvalue' => isset( $current['labels']['use_featured_image'] ) ? esc_attr( $current['labels']['use_featured_image'] ) : '',
							'aftertext' => esc_html__( '(e.g. Use as featured image for this movie)', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Use as featured image for this %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Archives', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Post type archive label used in nav menus.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'archives',
							'textvalue' => isset( $current['labels']['archives'] ) ? esc_attr( $current['labels']['archives'] ) : '',
							'aftertext' => esc_html__( '(e.g. Movie archives)', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( '%s archives', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Insert into item', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used as the "Insert into post" or "Insert into page" phrase for the post type.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'insert_into_item',
							'textvalue' => isset( $current['labels']['insert_into_item'] ) ? esc_attr( $current['labels']['insert_into_item'] ) : '',
							'aftertext' => esc_html__( '(e.g. Insert into movie)', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Insert into %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Uploaded to this Item', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used as the "Uploaded to this post" or "Uploaded to this page" phrase for the post type.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'uploaded_to_this_item',
							'textvalue' => isset( $current['labels']['uploaded_to_this_item'] ) ? esc_attr( $current['labels']['uploaded_to_this_item'] ) : '',
							'aftertext' => esc_html__( '(e.g. Uploaded to this movie)', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Upload to this %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Filter Items List', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Screen reader text for the filter links heading on the post type listing screen.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'filter_items_list',
							'textvalue' => isset( $current['labels']['filter_items_list'] ) ? esc_attr( $current['labels']['filter_items_list'] ) : '',
							'aftertext' => esc_html__( '(e.g. Filter movies list)', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Filter %s list', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Items List Navigation', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Screen reader text for the pagination heading on the post type listing screen.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'items_list_navigation',
							'textvalue' => isset( $current['labels']['items_list_navigation'] ) ? esc_attr( $current['labels']['items_list_navigation'] ) : '',
							'aftertext' => esc_html__( '(e.g. Movies list navigation)', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( '%s list navigation', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Items List', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Screen reader text for the items list heading on the post type listing screen.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'items_list',
							'textvalue' => isset( $current['labels']['items_list'] ) ? esc_attr( $current['labels']['items_list'] ) : '',
							'aftertext' => esc_html__( '(e.g. Movies list)', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( '%s list', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Attributes', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used for the title of the post attributes meta box.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'attributes',
							'textvalue' => isset( $current['labels']['attributes'] ) ? esc_attr( $current['labels']['attributes'] ) : '',
							'aftertext' => esc_html__( '(e.g. Movies Attributes)', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( '%s attributes', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( '"New" menu in admin bar', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used in New in Admin menu bar. Default "singular name" label.', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'name_admin_bar',
							'textvalue' => isset( $current['labels']['name_admin_bar'] ) ? esc_attr( $current['labels']['name_admin_bar'] ) : '',
							'aftertext' => esc_html__( '(e.g. Movie)', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => 'item', // not localizing because it's so isolated.
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Item Published', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used in the editor notice after publishing a post. Default "Post published." / "Page published."', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'item_published',
							'textvalue' => isset( $current['labels']['item_published'] ) ? esc_attr( $current['labels']['item_published'] ) : '',
							'aftertext' => esc_html__( '(e.g. Movie published)', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( '%s published', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Item Published Privately', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used in the editor notice after publishing a private post. Default "Post published privately." / "Page published privately."', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'item_published_privately',
							'textvalue' => isset( $current['labels']['item_published_privately'] ) ? esc_attr( $current['labels']['item_published_privately'] ) : '',
							'aftertext' => esc_html__( '(e.g. Movie published privately.)', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( '%s published privately.', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Item Reverted To Draft', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used in the editor notice after reverting a post to draft. Default "Post reverted to draft." / "Page reverted to draft."', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'item_reverted_to_draft',
							'textvalue' => isset( $current['labels']['item_reverted_to_draft'] ) ? esc_attr( $current['labels']['item_reverted_to_draft'] ) : '',
							'aftertext' => esc_html__( '(e.g. Movie reverted to draft)', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( '%s reverted to draft.', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Item Scheduled', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used in the editor notice after scheduling a post to be published at a later date. Default "Post scheduled." / "Page scheduled."', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'item_scheduled',
							'textvalue' => isset( $current['labels']['item_scheduled'] ) ? esc_attr( $current['labels']['item_scheduled'] ) : '',
							'aftertext' => esc_html__( '(e.g. Movie scheduled)', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( '%s scheduled', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'labeltext' => esc_html__( 'Item Updated', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used in the editor notice after updating a post. Default "Post updated." / "Page updated."', 'dt-the7-core' ),
							'namearray' => Post_Types_Handler::FIELD_POST_TYPE_LABELS,
							'name'      => 'item_updated',
							'textvalue' => isset( $current['labels']['item_updated'] ) ? esc_attr( $current['labels']['item_updated'] ) : '',
							'aftertext' => esc_html__( '(e.g. Movie updated)', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( '%s updated.', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						?>
					</table>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * @param  \The7_Core\Mods\Post_Type_Builder\Utility\UI  $ui
	 * @param  array  $current
	 *
	 * @return void
	 */
	protected static function render_additional_settings( $ui, $current ) {
		$select             = [
			'options' => [
				[ 'attr' => '0', 'text' => esc_attr__( 'False', 'dt-the7-core' ) ],
				[
					'attr'    => '1',
					'text'    => esc_attr__( 'True', 'dt-the7-core' ),
					'default' => 'true'
				],
			],
		];
		$selected           = isset( $current ) ? Utility::disp_boolean( $current['public'] ) : '';
		$select['selected'] = ! empty( $selected ) ? $current['public'] : '';
		echo $ui->get_select_input( [
			'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
			'name'       => 'public',
			'labeltext'  => esc_html__( 'Public', 'dt-the7-core' ),
			'aftertext'  => esc_html__( '(Custom Post Type UI default: true) Whether or not posts of this type should be shown in the admin UI and is publicly queryable.', 'dt-the7-core' ),
			'selections' => $select,
		] );

		$select             = [
			'options' => [
				[ 'attr' => '0', 'text' => esc_attr__( 'False', 'dt-the7-core' ) ],
				[
					'attr'    => '1',
					'text'    => esc_attr__( 'True', 'dt-the7-core' ),
					'default' => 'true'
				],
			],
		];
		$selected           = isset( $current ) ? Utility::disp_boolean( $current['show_ui'] ) : '';
		$select['selected'] = ! empty( $selected ) ? $current['show_ui'] : '';
		echo $ui->get_select_input( [
			'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
			'name'       => 'show_ui',
			'labeltext'  => esc_html__( 'Show UI', 'dt-the7-core' ),
			'aftertext'  => esc_html__( '(default: true) Whether or not to generate a default UI for managing this post type.', 'dt-the7-core' ),
			'selections' => $select,
		] );

		$select             = [
			'options' => [
				[ 'attr' => '0', 'text' => esc_attr__( 'False', 'dt-the7-core' ) ],
				[
					'attr'    => '1',
					'text'    => esc_attr__( 'True', 'dt-the7-core' ),
					'default' => 'true'
				],
			],
		];
		$selected           = isset( $current ) && ! empty( $current['show_in_nav_menus'] ) ? Utility::disp_boolean( $current['show_in_nav_menus'] ) : '';
		$select['selected'] = ( ! empty( $selected ) && ! empty( $current['show_in_nav_menus'] ) ) ? $current['show_in_nav_menus'] : '';
		echo $ui->get_select_input( [
			'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
			'name'       => 'show_in_nav_menus',
			'labeltext'  => esc_html__( 'Show in Nav Menus', 'dt-the7-core' ),
			'aftertext'  => esc_html__( '(Custom Post Type UI default: true) Whether or not this post type is available for selection in navigation menus.', 'dt-the7-core' ),
			'selections' => $select,
		] );

		$select             = [
			'options' => [
				[ 'attr' => '0', 'text' => esc_attr__( 'False', 'dt-the7-core' ) ],
				[
					'attr'    => '1',
					'text'    => esc_attr__( 'True', 'dt-the7-core' ),
					'default' => 'true'
				],
			],
		];
		$selected           = ( isset( $current ) && ! empty( $current['show_in_rest'] ) ) ? Utility::disp_boolean( $current['show_in_rest'] ) : '';
		$select['selected'] = ( ! empty( $selected ) && ! empty( $current['show_in_rest'] ) ) ? $current['show_in_rest'] : '';
		echo $ui->get_select_input( [
			'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
			'name'       => 'show_in_rest',
			'labeltext'  => esc_html__( 'Show in REST API', 'dt-the7-core' ),
			'aftertext'  => esc_html__( '(Custom Post Type UI default: true) Whether or not to show this post type data in the WP REST API.', 'dt-the7-core' ),
			'selections' => $select,
		] );

		echo $ui->get_text_input( [
			'namearray' => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
			'name'      => 'rest_base',
			'labeltext' => esc_html__( 'REST API base slug', 'dt-the7-core' ),
			'aftertext' => esc_attr__( 'Slug to use in REST API URLs.', 'dt-the7-core' ),
			'textvalue' => isset( $current['rest_base'] ) ? esc_attr( $current['rest_base'] ) : '',
		] );

		echo $ui->get_text_input( [
			'namearray' => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
			'name'      => 'rest_controller_class',
			'labeltext' => esc_html__( 'REST API controller class', 'dt-the7-core' ),
			'aftertext' => esc_attr__( '(default: WP_REST_Posts_Controller) Custom controller to use instead of WP_REST_Posts_Controller.', 'dt-the7-core' ),
			'textvalue' => isset( $current['rest_controller_class'] ) ? esc_attr( $current['rest_controller_class'] ) : '',
		] );

		$select             = [
			'options' => [
				[
					'attr'    => '0',
					'text'    => esc_attr__( 'False', 'dt-the7-core' ),
					'default' => 'true'
				],
				[ 'attr' => '1', 'text' => esc_attr__( 'True', 'dt-the7-core' ) ],
			],
		];
		$selected           = isset( $current ) ? Utility::disp_boolean( $current['hierarchical'] ) : '';
		$select['selected'] = ! empty( $selected ) ? $current['hierarchical'] : '';
		echo $ui->get_select_input( [
			'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
			'name'       => 'hierarchical',
			'labeltext'  => esc_html__( 'Hierarchical', 'dt-the7-core' ),
			'aftertext'  => esc_html__( '(default: false) Whether or not the post type can have parent-child relationships. At least one published content item is needed in order to select a parent.', 'dt-the7-core' ),
			'selections' => $select,
		] );

		$select             = [
			'options' => [
				[ 'attr' => '0', 'text' => esc_attr__( 'False', 'dt-the7-core' ) ],
				[
					'attr'    => '1',
					'text'    => esc_attr__( 'True', 'dt-the7-core' ),
					'default' => 'true'
				],
			],
		];
		$selected           = isset( $current ) ? Utility::disp_boolean( $current['query_var'] ) : '';
		$select['selected'] = ! empty( $selected ) ? $current['query_var'] : '';
		echo $ui->get_select_input( [
			'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
			'name'       => 'query_var',
			'labeltext'  => esc_html__( 'Query Var', 'dt-the7-core' ),
			'aftertext'  => esc_html__( '(default: true) Sets the query_var key for this post type.', 'dt-the7-core' ),
			'selections' => $select,
		] );

		echo $ui->get_text_input( [
			'namearray' => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
			'name'      => 'query_var_slug',
			'textvalue' => isset( $current['query_var_slug'] ) ? esc_attr( $current['query_var_slug'] ) : '',
			'labeltext' => esc_html__( 'Custom Query Var Slug', 'dt-the7-core' ),
			'aftertext' => esc_attr__( '(default: post type slug) Query var needs to be true to use.', 'dt-the7-core' ),
			'helptext'  => esc_html__( 'Custom query var slug to use instead of the default.', 'dt-the7-core' ),
		] );

		echo $ui->get_tr_start() . $ui->get_th_start();
		echo $ui->get_label( 'show_in_menu', esc_html__( 'Show in Menu', 'dt-the7-core' ) );
		echo $ui->get_p( esc_html__( '"Show UI" must be "true". If an existing top level page such as "tools.php" is indicated for second input, post type will be sub menu of that.', 'dt-the7-core' ) );
		echo $ui->get_th_end() . $ui->get_td_start();

		$select             = [
			'options' => [
				[ 'attr' => '0', 'text' => esc_attr__( 'False', 'dt-the7-core' ) ],
				[
					'attr'    => '1',
					'text'    => esc_attr__( 'True', 'dt-the7-core' ),
					'default' => 'true'
				],
			],
		];
		$selected           = isset( $current ) ? Utility::disp_boolean( $current['show_in_menu'] ) : '';
		$select['selected'] = ! empty( $selected ) ? $current['show_in_menu'] : '';
		echo $ui->get_select_input( [
			'namearray'  => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
			'name'       => 'show_in_menu',
			'aftertext'  => esc_html__( '(default: true) Whether or not to show the post type in the admin menu and where to show that menu.', 'dt-the7-core' ),
			'selections' => $select,
			'wrap'       => false,
		] );

		echo '<br/>';

		echo $ui->get_text_input( [
			'namearray'      => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
			'name'           => 'show_in_menu_string',
			'textvalue'      => isset( $current['show_in_menu_string'] ) ? esc_attr( $current['show_in_menu_string'] ) : '',
			'helptext'       => esc_attr__( 'The top-level admin menu page file name for which the post type should be in the sub menu of.', 'dt-the7-core' ),
			'helptext_after' => true,
			'wrap'           => false,
		] );
		echo $ui->get_td_end() . $ui->get_tr_end();

		echo $ui->get_tr_start() . $ui->get_th_start() . '<label for="custom_supports">' . esc_html__( 'Custom "Supports"', 'dt-the7-core' ) . '</label>';
		echo $ui->get_p( esc_html__( 'Use this input to register custom "supports" values, separated by commas.', 'dt-the7-core' ) );
		echo $ui->get_th_end() . $ui->get_td_start();
		echo $ui->get_text_input( [
			'namearray'      => Post_Types_Handler::FIELD_POST_TYPE_ARGS,
			'name'           => 'custom_supports',
			'textvalue'      => isset( $current['custom_supports'] ) ? esc_attr( $current['custom_supports'] ) : '',
			'helptext'       => esc_attr__( 'Provide custom support slugs here.', 'dt-the7-core' ),
			'helptext_after' => true,
			'wrap'           => false,
		] );
		echo $ui->get_td_end() . $ui->get_tr_end();
	}

	/**
	 * @param string $action
	 *
	 * @return void
	 */
	protected static function render_control_buttons( $action = null ) {
		?>

		<input type="submit" class="button-primary" name="<?php echo esc_attr( Handler::POST_UPDATE ) ?>" value="<?php esc_attr_e( 'Save Post Type', 'dt-the7-core' ) ?>" />

		<?php
		if ( $action === 'edit' ) {
			?>
			<input type="submit" class="button-secondary the7-delete-bottom" name="<?php echo esc_attr( Handler::POST_DELETE ) ?>" value="<?php esc_attr_e( 'Delete Post Type', 'dt-the7-core' ) ?>" />
		<?php } elseif ( $action === 'restore' ) { ?>
			<input type="submit" class="button-secondary" name="<?php echo esc_attr( Handler::POST_DELETE ) ?>" value="<?php esc_attr_e( 'Restore to default', 'dt-the7-core' ) ?>" />
			<?php
		}
	}
}
