<?php

namespace The7_Core\Mods\Post_Type_Builder\Screens;

use The7_Core\Mods\Post_Type_Builder\Admin_Page;
use The7_Core\Mods\Post_Type_Builder\Handlers\Taxonomies_Handler;
use The7_Core\Mods\Post_Type_Builder\Handlers\Handler;
use The7_Core\Mods\Post_Type_Builder\Models\Taxonomies;
use The7_Core\Mods\Post_Type_Builder\Utility\Utility;

defined( 'ABSPATH' ) || exit;

class Edit_Taxonomy {

	/**
	 * @param array|null $data
	 *
	 * @return void
	 */
	public static function render( $data = null ) {
		$tab     = $data ? 'edit' : 'new';
		$current = $data;
		if ( $current === null ) {
			$default = Taxonomies::create_item( [] );
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
					esc_html_e( 'New Taxonomy', 'dt-the7-core' );
				} else {
					esc_html_e( 'Edit Taxonomy', 'dt-the7-core' );
				}
				?>
			</h1>
			<a href="<?php echo esc_url( Admin_Page::get_link() ) ?>" class="page-title-action"><?php esc_html_e( 'Items list', 'dt-the7-core' ) ?></a>

			<hr class="wp-header-end">

			<?php
			$ui = new \The7_Core\Mods\Post_Type_Builder\Utility\UI();
			?>

			<form class="the7-taxonomies-ui" method="post" action="">
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
										echo $ui->get_label( 'name', esc_html__( 'Taxonomy Slug', 'dt-the7-core' ) ) . $ui->get_required_span();

										if ( 'edit' === $tab ) {
											echo '<p id="slugchanged" class="hidemessage">' . esc_html__( 'Slug has changed', 'dt-the7-core' ) . '<span class="dashicons dashicons-warning"></span></p>';
										}
										echo '<p id="slugexists" class="hidemessage">' . esc_html__( 'Slug already exists', 'dt-the7-core' ) . '<span class="dashicons dashicons-warning"></span></p>';

										echo $ui->get_th_end() . $ui->get_td_start();

										echo $ui->get_text_input( [
											'namearray'   => Taxonomies_Handler::FIELD_TAX_ARGS,
											'name'        => 'name',
											'textvalue'   => isset( $current['name'] ) ? esc_attr( $current['name'] ) : '',
											'maxlength'   => '32',
											'helptext'    => esc_attr__( 'The taxonomy name/slug. Used for various queries for taxonomy content.', 'dt-the7-core' ),
											'required'    => true,
											'placeholder' => false,
											'wrap'        => false,
											'readonly'	  => $is_bundled
										] );

										echo '<p class="the7-slug-details">';
										esc_html_e( 'Slugs should only contain alphanumeric, latin characters. Underscores should be used in place of spaces. Set "Custom Rewrite Slug" field to make slug use dashes for URLs.', 'dt-the7-core' );
										echo '</p>';

										if ( 'edit' === $tab && ! $is_bundled ) {
											echo '<p>';
											esc_html_e( 'DO NOT EDIT the taxonomy slug unless also planning to migrate terms. Changing the slug registers a new taxonomy entry.', 'dt-the7-core' );
											echo '</p>';

											echo '<div class="the7-spacer">';
											echo $ui->get_check_input( [
												'checkvalue' => 'update_taxonomy',
												'checked'    => true,
												'name'       => 'update_taxonomy',
												'namearray'  => Taxonomies_Handler::FIELD_TAX_UPDATE,
												'labeltext'  => esc_html__( 'Migrate terms to newly renamed taxonomy?', 'dt-the7-core' ),
												'helptext'   => '',
												'wrap'       => false,
											] );
											echo '</div>';
										}

										echo $ui->get_text_input( [
											'namearray' => Taxonomies_Handler::FIELD_TAX_ARGS,
											'name'      => 'singular_label',
											'textvalue' => isset( $current['singular_label'] ) ? esc_attr( $current['singular_label'] ) : '',
											'aftertext' => esc_html__( '(e.g. Actor)', 'dt-the7-core' ),
											'labeltext' => esc_html__( 'Singular Label', 'dt-the7-core' ),
											'helptext'  => esc_attr__( 'Used when a singular label is needed.', 'dt-the7-core' ),
											'required'  => true,
										] );

										echo $ui->get_text_input( [
											'namearray' => Taxonomies_Handler::FIELD_TAX_ARGS,
											'name'      => 'label',
											'textvalue' => isset( $current['label'] ) ? esc_attr( $current['label'] ) : '',
											'aftertext' => esc_html__( '(e.g. Actors)', 'dt-the7-core' ),
											'labeltext' => esc_html__( 'Plural Label', 'dt-the7-core' ),
											'helptext'  => esc_attr__( 'Used for the taxonomy admin menu item.', 'dt-the7-core' ),
											'required'  => true,
										] );

										echo $ui->get_td_end() . $ui->get_tr_end();

										$legend = $ui->get_legend_start() . esc_html__( 'Post type options', 'dt-the7-core' ) . $ui->get_legend_end();

										if ( $is_bundled ) {
											echo $ui->get_tr_start() . $ui->get_th_start() . esc_html__( 'Attached Post Types', 'dt-the7-core' );
											echo $ui->get_th_end() . $ui->get_td_start() . $ui->get_fieldset_start();

											echo $legend;
											self::render_predefined_post_types( $ui, $current );

											echo $ui->get_fieldset_end() . $ui->get_td_end() . $ui->get_tr_end();
										} else {
											echo $ui->get_tr_start() . $ui->get_th_start() . esc_html__( 'Attach to Post Type', 'dt-the7-core' );
											echo $ui->get_p( esc_html__( 'Add support for available registered post types. Only public post types listed by default.', 'dt-the7-core' ) );
											echo $ui->get_th_end() . $ui->get_td_start() . $ui->get_fieldset_start();

											echo $legend;
											self::render_all_post_types( $ui, $current );

											echo $ui->get_fieldset_end() . $ui->get_td_end() . $ui->get_tr_end();
										}
										?>
									</table>
									<p class="submit">
										<?php
										Taxonomies_Handler::nonce_field();

										self::render_control_buttons( $buttons_style );
										?>

										<?php if ( ! empty( $current ) ) { ?>
											<input type="hidden" name="<?php echo esc_attr( Taxonomies_Handler::FIELD_TAX_ORIGINAL ) ?>" id="tax_original" value="<?php echo esc_attr( $current['name'] ); ?>" />
											<?php
										}

										// Used to check and see if we should prevent duplicate slugs.
										?>

										<input type="hidden" name="<?php echo esc_attr( Taxonomies_Handler::FIELD_TAX_STATUS ) ?>" id="cpt_tax_status" value="<?php echo esc_attr( $tab ); ?>" />

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
										<span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Settings', 'dt-the7-core' ); ?></span>
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
											'namearray'  => Taxonomies_Handler::FIELD_TAX_ARGS,
											'name'       => 'rewrite',
											'labeltext'  => esc_html__( 'Rewrite', 'dt-the7-core' ),
											'aftertext'  => esc_html__( '(default: true) Whether or not WordPress should use rewrites for this taxonomy.', 'dt-the7-core' ),
											'options' => $true_false_options,
											'default' => '1',
											'selected' => $current['rewrite'],
										] );

										echo $ui->get_text_input( [
											'namearray' => Taxonomies_Handler::FIELD_TAX_ARGS,
											'name'      => 'rewrite_slug',
											'textvalue' => isset( $current['rewrite_slug'] ) ? esc_attr( $current['rewrite_slug'] ) : '',
											'aftertext' => esc_attr__( '(default: taxonomy name)', 'dt-the7-core' ),
											'labeltext' => esc_html__( 'Custom Rewrite Slug', 'dt-the7-core' ),
											'helptext'  => esc_html__( 'Custom taxonomy rewrite slug.', 'dt-the7-core' ),
										] );

										echo $ui->get_switch( [
											'namearray'  => Taxonomies_Handler::FIELD_TAX_ARGS,
											'name'       => 'rewrite_withfront',
											'labeltext'  => esc_html__( 'Rewrite With Front', 'dt-the7-core' ),
											'aftertext'  => esc_html__( '(default: true) Should the permastruct be prepended with the front base.', 'dt-the7-core' ),
											'options' => $true_false_options,
											'default' => '1',
											'selected' => $current['rewrite_withfront'],
										] );

										echo $ui->get_switch( [
											'namearray'  => Taxonomies_Handler::FIELD_TAX_ARGS,
											'name'       => 'rewrite_hierarchical',
											'labeltext'  => esc_html__( 'Rewrite Hierarchical', 'dt-the7-core' ),
											'aftertext'  => esc_html__( '(default: false) Should the permastruct allow hierarchical urls.', 'dt-the7-core' ),
											'options' => $true_false_options,
											'default' => '0',
											'selected' => $current['rewrite_hierarchical'],
										] );

										echo $ui->get_text_input( [
											'namearray' => Taxonomies_Handler::FIELD_TAX_ARGS,
											'name'      => 'default_term',
											'textvalue' => isset( $current['default_term'] ) ? esc_attr( $current['default_term'] ) : '',
											'labeltext' => esc_html__( 'Default Term', 'dt-the7-core' ),
											'helptext'  => esc_html__( 'Set a default term for the taxonomy. Able to set a name, slug, and description. Only a name is required if setting a default, others are optional. Set values in the following order, separated by comma. Example: name, slug, description', 'dt-the7-core' ),
										] );

										if ( ! $is_bundled ) {
											echo $ui->get_switch( [
												'namearray'  => Taxonomies_Handler::FIELD_TAX_ARGS,
												'name'       => 'hierarchical',
												'labeltext'  => esc_html__( 'Hierarchical', 'dt-the7-core' ),
												'aftertext'  => esc_html__( '(default: false) Whether the taxonomy can have parent-child relationships.', 'dt-the7-core' ),
												'options' => $true_false_options,
												'default' => '0',
												'selected' => $current['hierarchical'],
											] );

											echo $ui->get_switch( [
												'namearray' => Taxonomies_Handler::FIELD_TAX_ARGS,
												'name'      => 'publicly_queryable',
												'labeltext' => esc_html__( 'Public Queryable', 'dt-the7-core' ),
												'aftertext' => esc_html__( '(default: true) Whether or not the taxonomy should be publicly queryable.', 'dt-the7-core' ),
												'options'   => $true_false_options,
												'default'   => '1',
												'selected'  => $current['publicly_queryable'],
											] );

											echo $ui->get_switch( [
												'namearray' => Taxonomies_Handler::FIELD_TAX_ARGS,
												'name'      => 'show_admin_column',
												'labeltext' => esc_html__( 'Show Admin Column', 'dt-the7-core' ),
												'aftertext' => esc_html__( '(default: false) Whether to allow automatic creation of taxonomy columns on associated post-types.', 'dt-the7-core' ),
												'options'   => $true_false_options,
												'default'   => '1',
												'selected'  => $current['show_admin_column'],
											] );

											echo $ui->get_switch( [
												'namearray' => Taxonomies_Handler::FIELD_TAX_ARGS,
												'name'      => 'show_in_quick_edit',
												'labeltext' => esc_html__( 'Show in quick/bulk edit panel.', 'dt-the7-core' ),
												'aftertext' => esc_html__( '(default: false) Whether to show the taxonomy in the quick/bulk edit panel.', 'dt-the7-core' ),
												'options'   => $true_false_options,
												'default'   => '1',
												'selected'  => $current['show_in_quick_edit'],
											] );

											echo $ui->get_switch( [
												'namearray'  => Taxonomies_Handler::FIELD_TAX_ARGS,
												'name'       => 'show_tagcloud',
												'labeltext'  => esc_html__( 'Show in tag cloud.', 'dt-the7-core' ),
												'aftertext'  => esc_html__( '(default: false) Whether to list the taxonomy in the Tag Cloud Widget controls.', 'dt-the7-core' ),
												'options' => $true_false_options,
												'default' => '0',
												'selected' => $current['show_tagcloud'],
											] );
										}
										?>
									</table>
								</div>
							</div>
						</div>

						<p class="submit">
							<?php self::render_control_buttons( $buttons_style ) ?>
						</p>

					</div>
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
						<span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Additional labels', 'dt-the7-core' ); ?></span>
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
							'namearray' => Taxonomies_Handler::FIELD_TAX_ARGS,
							'name'      => 'description',
							'rows'      => '4',
							'cols'      => '40',
							'textvalue' => isset( $current['description'] ) ? esc_textarea( $current['description'] ) : '',
							'labeltext' => esc_html__( 'Description', 'dt-the7-core' ),
							'helptext'  => esc_attr__( 'Describe what your taxonomy is used for.', 'dt-the7-core' ),
						] );

						echo $ui->get_text_input( [
							'namearray' => Taxonomies_Handler::FIELD_TAX_LABELS,
							'name'      => 'menu_name',
							'textvalue' => isset( $current['labels']['menu_name'] ) ? esc_attr( $current['labels']['menu_name'] ) : '',
							'aftertext' => esc_attr__( '(e.g. Actors)', 'dt-the7-core' ),
							'labeltext' => esc_html__( 'Menu Name', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Custom admin menu name for your taxonomy.', 'dt-the7-core' ),
							'data' => [
								'label'     => 'item', // Not localizing because it's isolated.
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'namearray' => Taxonomies_Handler::FIELD_TAX_LABELS,
							'name'      => 'all_items',
							'textvalue' => isset( $current['labels']['all_items'] ) ? esc_attr( $current['labels']['all_items'] ) : '',
							'aftertext' => esc_attr__( '(e.g. All Actors)', 'dt-the7-core' ),
							'labeltext' => esc_html__( 'All Items', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used as tab text when showing all terms for hierarchical taxonomy while editing post.', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'All %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'namearray' => Taxonomies_Handler::FIELD_TAX_LABELS,
							'name'      => 'edit_item',
							'textvalue' => isset( $current['labels']['edit_item'] ) ? esc_attr( $current['labels']['edit_item'] ) : '',
							'aftertext' => esc_attr__( '(e.g. Edit Actor)', 'dt-the7-core' ),
							'labeltext' => esc_html__( 'Edit Item', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used at the top of the term editor screen for an existing taxonomy term.', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Edit %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'namearray' => Taxonomies_Handler::FIELD_TAX_LABELS,
							'name'      => 'view_item',
							'textvalue' => isset( $current['labels']['view_item'] ) ? esc_attr( $current['labels']['view_item'] ) : '',
							'aftertext' => esc_attr__( '(e.g. View Actor)', 'dt-the7-core' ),
							'labeltext' => esc_html__( 'View Item', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used in the admin bar when viewing editor screen for an existing taxonomy term.', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'View %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'namearray' => Taxonomies_Handler::FIELD_TAX_LABELS,
							'name'      => 'update_item',
							'textvalue' => isset( $current['labels']['update_item'] ) ? esc_attr( $current['labels']['update_item'] ) : '',
							'aftertext' => esc_attr__( '(e.g. Update Actor Name)', 'dt-the7-core' ),
							'labeltext' => esc_html__( 'Update Item Name', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Custom taxonomy label. Used in the admin menu for displaying taxonomies.', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Update %s name', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'namearray' => Taxonomies_Handler::FIELD_TAX_LABELS,
							'name'      => 'add_new_item',
							'textvalue' => isset( $current['labels']['add_new_item'] ) ? esc_attr( $current['labels']['add_new_item'] ) : '',
							'aftertext' => esc_attr__( '(e.g. Add New Actor)', 'dt-the7-core' ),
							'labeltext' => esc_html__( 'Add New Item', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used at the top of the term editor screen and button text for a new taxonomy term.', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Add new %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'namearray' => Taxonomies_Handler::FIELD_TAX_LABELS,
							'name'      => 'new_item_name',
							'textvalue' => isset( $current['labels']['new_item_name'] ) ? esc_attr( $current['labels']['new_item_name'] ) : '',
							'aftertext' => esc_attr__( '(e.g. New Actor Name)', 'dt-the7-core' ),
							'labeltext' => esc_html__( 'New Item Name', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Custom taxonomy label. Used in the admin menu for displaying taxonomies.', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'New %s name', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'namearray' => Taxonomies_Handler::FIELD_TAX_LABELS,
							'name'      => 'parent_item',
							'textvalue' => isset( $current['labels']['parent_item'] ) ? esc_attr( $current['labels']['parent_item'] ) : '',
							'aftertext' => esc_attr__( '(e.g. Parent Actor)', 'dt-the7-core' ),
							'labeltext' => esc_html__( 'Parent Item', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Custom taxonomy label. Used in the admin menu for displaying taxonomies.', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Parent %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'namearray' => Taxonomies_Handler::FIELD_TAX_LABELS,
							'name'      => 'parent_item_colon',
							'textvalue' => isset( $current['labels']['parent_item_colon'] ) ? esc_attr( $current['labels']['parent_item_colon'] ) : '',
							'aftertext' => esc_attr__( '(e.g. Parent Actor:)', 'dt-the7-core' ),
							'labeltext' => esc_html__( 'Parent Item Colon', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Custom taxonomy label. Used in the admin menu for displaying taxonomies.', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Parent %s:', 'dt-the7-core' ), 'item' ),
								'plurality' => 'singular',
							],
						] );

						echo $ui->get_text_input( [
							'namearray' => Taxonomies_Handler::FIELD_TAX_LABELS,
							'name'      => 'search_items',
							'textvalue' => isset( $current['labels']['search_items'] ) ? esc_attr( $current['labels']['search_items'] ) : '',
							'aftertext' => esc_attr__( '(e.g. Search Actors)', 'dt-the7-core' ),
							'labeltext' => esc_html__( 'Search Items', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Custom taxonomy label. Used in the admin menu for displaying taxonomies.', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Search %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'namearray' => Taxonomies_Handler::FIELD_TAX_LABELS,
							'name'      => 'popular_items',
							'textvalue' => isset( $current['labels']['popular_items'] ) ? esc_attr( $current['labels']['popular_items'] ) : null,
							'aftertext' => esc_attr__( '(e.g. Popular Actors)', 'dt-the7-core' ),
							'labeltext' => esc_html__( 'Popular Items', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Custom taxonomy label. Used in the admin menu for displaying taxonomies.', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Popular %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'namearray' => Taxonomies_Handler::FIELD_TAX_LABELS,
							'name'      => 'separate_items_with_commas',
							'textvalue' => isset( $current['labels']['separate_items_with_commas'] ) ? esc_attr( $current['labels']['separate_items_with_commas'] ) : null,
							'aftertext' => esc_attr__( '(e.g. Separate Actors with commas)', 'dt-the7-core' ),
							'labeltext' => esc_html__( 'Separate Items with Commas', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Custom taxonomy label. Used in the admin menu for displaying taxonomies.', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Separate %s with commas', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'namearray' => Taxonomies_Handler::FIELD_TAX_LABELS,
							'name'      => 'add_or_remove_items',
							'textvalue' => isset( $current['labels']['add_or_remove_items'] ) ? esc_attr( $current['labels']['add_or_remove_items'] ) : null,
							'aftertext' => esc_attr__( '(e.g. Add or remove Actors)', 'dt-the7-core' ),
							'labeltext' => esc_html__( 'Add or Remove Items', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Custom taxonomy label. Used in the admin menu for displaying taxonomies.', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Add or remove %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'namearray' => Taxonomies_Handler::FIELD_TAX_LABELS,
							'name'      => 'choose_from_most_used',
							'textvalue' => isset( $current['labels']['choose_from_most_used'] ) ? esc_attr( $current['labels']['choose_from_most_used'] ) : null,
							'aftertext' => esc_attr__( '(e.g. Choose from the most used Actors)', 'dt-the7-core' ),
							'labeltext' => esc_html__( 'Choose From Most Used', 'dt-the7-core' ),
							'helptext'  => esc_attr__( 'The text displayed via clicking ‘Choose from the most used items’ in the taxonomy meta box when no items are available.', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Choose from the most used %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'namearray' => Taxonomies_Handler::FIELD_TAX_LABELS,
							'name'      => 'not_found',
							'textvalue' => isset( $current['labels']['not_found'] ) ? esc_attr( $current['labels']['not_found'] ) : null,
							'aftertext' => esc_attr__( '(e.g. No Actors found)', 'dt-the7-core' ),
							'labeltext' => esc_html__( 'Not found', 'dt-the7-core' ),
							'helptext'  => esc_html__( 'Used when indicating that there are no terms in the given taxonomy within the meta box and taxonomy list table.', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'No %s found', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'namearray' => Taxonomies_Handler::FIELD_TAX_LABELS,
							'name'      => 'no_terms',
							'textvalue' => isset( $current['labels']['no_terms'] ) ? esc_attr( $current['labels']['no_terms'] ) : null,
							'aftertext' => esc_html__( '(e.g. No actors)', 'dt-the7-core' ),
							'labeltext' => esc_html__( 'No terms', 'dt-the7-core' ),
							'helptext'  => esc_attr__( 'Used when indicating that there are no terms in the given taxonomy associated with an object.', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'No %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'namearray' => Taxonomies_Handler::FIELD_TAX_LABELS,
							'name'      => 'items_list_navigation',
							'textvalue' => isset( $current['labels']['items_list_navigation'] ) ? esc_attr( $current['labels']['items_list_navigation'] ) : null,
							'aftertext' => esc_html__( '(e.g. Actors list navigation)', 'dt-the7-core' ),
							'labeltext' => esc_html__( 'Items List Navigation', 'dt-the7-core' ),
							'helptext'  => esc_attr__( 'Screen reader text for the pagination heading on the term listing screen.', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( '%s list navigation', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'namearray' => Taxonomies_Handler::FIELD_TAX_LABELS,
							'name'      => 'items_list',
							'textvalue' => isset( $current['labels']['items_list'] ) ? esc_attr( $current['labels']['items_list'] ) : null,
							'aftertext' => esc_html__( '(e.g. Actors list)', 'dt-the7-core' ),
							'labeltext' => esc_html__( 'Items List', 'dt-the7-core' ),
							'helptext'  => esc_attr__( 'Screen reader text for the items list heading on the term listing screen.', 'dt-the7-core' ),
							'data' => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( '%s list', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
							],
						] );

						echo $ui->get_text_input( [
							'namearray' => Taxonomies_Handler::FIELD_TAX_LABELS,
							'name'      => 'back_to_items',
							'textvalue' => isset( $current['labels']['back_to_items'] ) ? esc_attr( $current['labels']['back_to_items'] ) : null,
							'aftertext' => esc_html__( '(e.g. &larr; Back to actors', 'dt-the7-core' ),
							'labeltext' => esc_html__( 'Back to Items', 'dt-the7-core' ),
							'helptext'  => esc_attr__( 'The text displayed after a term has been updated for a link back to main index.', 'dt-the7-core' ),
							'data'      => [
								/* translators: Used for autofill */
								'label'     => sprintf( esc_attr__( 'Back to %s', 'dt-the7-core' ), 'item' ),
								'plurality' => 'plural',
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
	 * @param array $current
	 */
	protected static function render_all_post_types( $ui, $current ) {
		$post_types = get_post_types( [ 'public' => true ], 'objects' );
		$core_label = ' ' . esc_html__( '(WP Core)', 'dt-the7-core' );

		foreach ( $post_types as $post_type ) {
			$label = $post_type->label;
			if ( in_array( $post_type->name, [ 'post', 'page', 'attachment' ], true ) ) {
				$label .= $core_label;
			}

			echo $ui->get_check_input( [
				'checkvalue' => $post_type->name,
				'checked'    => ( ! empty( $current['object_types'] ) && in_array( $post_type->name, (array) $current['object_types'], true ) ),
				'name'       => $post_type->name,
				'namearray'  => Taxonomies_Handler::FIELD_TAX_RELATIONS,
				'textvalue'  => $post_type->name,
				'labeltext'  => $label,
				'wrap'       => false,
			] );
		}
	}

	/**
	 * @param  \The7_Core\Mods\Post_Type_Builder\Utility\UI  $ui
	 * @param array $current
	 */
	protected static function render_predefined_post_types( $ui, $current ) {
		$predefined = (array) $current['object_types'];

		if ( ! $predefined ) {
			return;
		}

		$post_types = get_post_types( [ 'public' => true ], 'objects' );
		foreach ( $post_types as $post_type ) {
			if ( ! in_array( $post_type->name, $predefined, true ) ) {
				continue;
			}

			echo $ui->get_check_input( [
				'checkvalue' => $post_type->name,
				'checked'    => true,
				'name'       => $post_type->name,
				'namearray'  => Taxonomies_Handler::FIELD_TAX_RELATIONS,
				'textvalue'  => $post_type->name,
				'labeltext'  => $post_type->label,
				'wrap'       => false,
				'disabled'	 => true,
			] );
		}
	}

	/**
	 * @param  \The7_Core\Mods\Post_Type_Builder\Utility\UI  $ui
	 * @param  array  $true_false_options
	 * @param  array  $current
	 *
	 * @return void
	 */
	protected static function render_additional_settings( $ui, $true_false_options, $current ) {
		echo $ui->get_switch( [
			'namearray' => Taxonomies_Handler::FIELD_TAX_ARGS,
			'name'      => 'public',
			'labeltext' => esc_html__( 'Public', 'dt-the7-core' ),
			'aftertext' => esc_html__( '(default: true) Whether a taxonomy is intended for use publicly either via the admin interface or by front-end users.', 'dt-the7-core' ),
			'options'   => $true_false_options,
			'default'   => '1',
			'selected'  => $current['public'],
		] );

		echo $ui->get_switch( [
			'namearray' => Taxonomies_Handler::FIELD_TAX_ARGS,
			'name'      => 'show_ui',
			'labeltext' => esc_html__( 'Show UI', 'dt-the7-core' ),
			'aftertext' => esc_html__( '(default: true) Whether to generate a default UI for managing this custom taxonomy.', 'dt-the7-core' ),
			'options'   => $true_false_options,
			'default'   => '1',
			'selected'  => $current['show_ui'],
		] );

		echo $ui->get_switch( [
			'namearray' => Taxonomies_Handler::FIELD_TAX_ARGS,
			'name'      => 'show_in_menu',
			'labeltext' => esc_html__( 'Show in menu', 'dt-the7-core' ),
			'aftertext' => esc_html__( '(default: value of show_ui) Whether to show the taxonomy in the admin menu.', 'dt-the7-core' ),
			'options'   => $true_false_options,
			'default'   => '1',
			'selected'  => $current['show_in_menu'],
		] );

		echo $ui->get_switch( [
			'namearray' => Taxonomies_Handler::FIELD_TAX_ARGS,
			'name'      => 'show_in_nav_menus',
			'labeltext' => esc_html__( 'Show in nav menus', 'dt-the7-core' ),
			'aftertext' => esc_html__( '(default: value of public) Whether to make the taxonomy available for selection in navigation menus.', 'dt-the7-core' ),
			'options'   => $true_false_options,
			'default'   => '1',
			'selected'  => $current['show_in_nav_menus'],
		] );

		echo $ui->get_switch( [
			'namearray' => Taxonomies_Handler::FIELD_TAX_ARGS,
			'name'      => 'query_var',
			'labeltext' => esc_html__( 'Query Var', 'dt-the7-core' ),
			'aftertext' => esc_html__( '(default: true) Sets the query_var key for this taxonomy.', 'dt-the7-core' ),
			'options'   => $true_false_options,
			'default'   => '1',
			'selected'  => $current['query_var'],
		] );

		echo $ui->get_text_input( [
			'namearray' => Taxonomies_Handler::FIELD_TAX_ARGS,
			'name'      => 'query_var_slug',
			'textvalue' => isset( $current['query_var_slug'] ) ? esc_attr( $current['query_var_slug'] ) : '',
			'aftertext' => esc_attr__( '(default: taxonomy slug). Query var needs to be true to use.', 'dt-the7-core' ),
			'labeltext' => esc_html__( 'Custom Query Var String', 'dt-the7-core' ),
			'helptext'  => esc_html__( 'Sets a custom query_var slug for this taxonomy.', 'dt-the7-core' ),
		] );

		echo $ui->get_switch( [
			'namearray' => Taxonomies_Handler::FIELD_TAX_ARGS,
			'name'      => 'show_in_rest',
			'labeltext' => esc_html__( 'Show in REST API', 'dt-the7-core' ),
			'aftertext' => esc_html__( '(Custom Post Type UI default: true) Whether to show this taxonomy data in the WP REST API.', 'dt-the7-core' ),
			'options'   => $true_false_options,
			'default'   => '1',
			'selected'  => $current['show_in_rest'],
		] );

		echo $ui->get_text_input( [
			'namearray' => Taxonomies_Handler::FIELD_TAX_ARGS,
			'name'      => 'rest_base',
			'labeltext' => esc_html__( 'REST API base slug', 'dt-the7-core' ),
			'helptext'  => esc_attr__( 'Slug to use in REST API URLs.', 'dt-the7-core' ),
			'textvalue' => isset( $current['rest_base'] ) ? esc_attr( $current['rest_base'] ) : '',
		] );

		echo $ui->get_text_input( [
			'namearray' => Taxonomies_Handler::FIELD_TAX_ARGS,
			'name'      => 'rest_controller_class',
			'labeltext' => esc_html__( 'REST API controller class', 'dt-the7-core' ),
			'aftertext' => esc_attr__( '(default: WP_REST_Terms_Controller) Custom controller to use instead of WP_REST_Terms_Controller.', 'dt-the7-core' ),
			'textvalue' => isset( $current['rest_controller_class'] ) ? esc_attr( $current['rest_controller_class'] ) : '',
		] );

		echo $ui->get_text_input( [
			'namearray' => Taxonomies_Handler::FIELD_TAX_ARGS,
			'name'      => 'meta_box_cb',
			'textvalue' => isset( $current['meta_box_cb'] ) ? esc_attr( $current['meta_box_cb'] ) : '',
			'labeltext' => esc_html__( 'Metabox callback', 'dt-the7-core' ),
			'helptext'  => esc_html__( 'Sets a callback function name for the meta box display. Hierarchical default: post_categories_meta_box, non-hierarchical default: post_tags_meta_box. To remove the metabox completely, use "false".', 'dt-the7-core' ),
		] );
	}

	/**
	 * @param string $action
	 *
	 * @return void
	 */
	protected static function render_control_buttons( $action = null ) {
		?>

		<input type="submit" class="button-primary" name="<?php echo esc_attr( Handler::POST_UPDATE ) ?>" value="<?php esc_attr_e( 'Save Taxonomy', 'dt-the7-core' ) ?>" />

		<?php
		if ( $action === 'edit' ) {
			?>
			<input type="submit" class="button-secondary the7-delete-bottom" name="<?php echo esc_attr( Handler::POST_DELETE ) ?>" value="<?php esc_attr_e( 'Delete Taxonomy', 'dt-the7-core' ) ?>" />
		<?php } elseif ( $action === 'restore' ) { ?>
			<input type="submit" class="button-secondary" name="<?php echo esc_attr( Handler::POST_DELETE ) ?>" value="<?php esc_attr_e( 'Restore to default', 'dt-the7-core' ) ?>" />
			<?php
		}
	}

}
