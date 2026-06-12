<?php

namespace The7_Core\Mods\Post_Type_Builder\Screens;

use The7_Core\Mods\Post_Type_Builder\Admin_Page;
use The7_Core\Mods\Post_Type_Builder\Handlers\Post_Types_Handler;
use The7_Core\Mods\Post_Type_Builder\Models\Taxonomies;
use The7_Core\Mods\Post_Type_Builder\Models\Post_Types;

defined( 'ABSPATH' ) || exit;

class Items_List {

	/**
	 * @return void
	 */
	public static function render() {
		?>

		<div class="wrap">
			<hr class="wp-header-end">

			<?php
			self::post_types_table();
			self::taxonomies();
			?>

		</div>

		<?php
	}

	/**
	 * @return void
	 */
	protected static function post_types_table() {
		$post_types = Post_Types::get_for_display() ?: [];
		ksort( $post_types );
		?>

		<h2 class="wp-heading-inline"><?php esc_html_e( 'Post Types', 'dt-the7-core' ) ?></h2>
		<a href="<?php echo esc_url( Admin_Page::get_post_type_link( Admin_Page::ACTION_NEW ) ) ?>" class="page-title-action"><?php esc_html_e( 'Add new', 'dt-the7-core' ) ?></a>

		<table class="wp-list-table widefat fixed striped table-view-list">
			<thead>
			<tr>
				<th scope="col" class="manage-column column-primary"><?php esc_html_e( 'Title', 'dt-the7-core' ) ?></th>
				<th scope="col" class="manage-column"><?php esc_html_e( 'Taxonomies', 'dt-the7-core' ) ?></th>
			</tr>
			</thead>

			<tbody>

			<?php
				foreach ( $post_types as $post_type ) :
					$is_predefined = ! empty( $post_type['predefined'] );
					$is_disabled = ! empty( $post_type['disabled'] );

					$title = self::get_item_label( $post_type )
			?>

				<tr>
					<td class="title column-title has-row-actions column-primary" data-colname="Title">
						<?php if ( $is_disabled ) : ?>

							<strong><span class="row-title" aria-label="<?php echo esc_attr( $title ) ?>"><?php echo esc_html( $title ) ?></span></strong>

						<?php else: ?>

							<strong><a class="row-title" href="<?php echo esc_url( Admin_Page::get_post_type_link( Admin_Page::ACTION_EDIT, $post_type['name'] ) ) ?>" aria-label="<?php echo esc_attr( $title ) ?>"><?php echo esc_html( $title ) ?></a></strong>

						<?php endif; ?>

						<?php
						if ( $is_disabled ) {
							$actions = [
								'activate' => '<a href="' . Post_Types_Handler::nonce_url( Admin_Page::get_post_type_link( Admin_Page::ACTION_ACTIVATE, $post_type['name'] ) ) . '" aria-label="">' . esc_html__( 'Activate', 'dt-the7-core' ) . '</a>',
							];
						} else {
							$actions = [
								'edit' => '<a href="' . esc_url( Admin_Page::get_post_type_link( Admin_Page::ACTION_EDIT, $post_type['name'] ) ) . '" aria-label="' . esc_attr( sprintf( esc_html__( 'Edit %s', 'dt-the7-core' ), $title ) ) . '">' . esc_html__( 'Edit', 'dt-the7-core' ) . '</a>',
							];

							if ( $post_type['public'] && $post_type['show_ui'] ) {
								$actions['view'] = '<a href="' . esc_url( admin_url( 'edit.php?post_type=' . $post_type['name'] ) ) . '" aria-label="' . esc_attr( sprintf( esc_html__( 'View %s post type', 'dt-the7-core' ), $title ) ) . '">' . esc_html__( 'View', 'dt-the7-core' ) . '</a>';
							}

							if ( $is_predefined ) {
								$actions['delete'] = '<a href="' . Post_Types_Handler::nonce_url( Admin_Page::get_post_type_link( Admin_Page::ACTION_DISABLE, $post_type['name'] ) ) . '" aria-label="">' . esc_html__( 'Deactivate', 'dt-the7-core' ) . '</a>';
							} else {
								$actions['delete'] = '<a href="' . Post_Types_Handler::nonce_url( Admin_Page::get_post_type_link( Admin_Page::ACTION_DELETE, $post_type['name'] ) ) . '" aria-label="">' . esc_html__( 'Delete', 'dt-the7-core' ) . '</a>';
							}
						}

						echo self::row_actions( $actions );
						?>
					</td>
					<td class="taxonomies" data-colname="Taxonomies">

						<?php
						if ( ! $is_disabled && ! empty( $post_type['taxonomies'] ) ) {
							$taxonomies = array_map( function ( $slug ) {
								$taxonomy = Taxonomies::get_for_display( $slug );
								if ( $taxonomy ) {
									return sprintf(
										'<a href="%s">%s</a>',
										esc_url( Admin_Page::get_taxonomy_link( Admin_Page::ACTION_EDIT, $slug ) ),
										esc_html( self::get_item_label( $taxonomy ) )
									);
								}

							   	$taxonomy = current( get_taxonomies( [ 'name' => $slug, 'public' => true ], 'objects' ) );
								if ( $taxonomy ) {
									return esc_html( $taxonomy->label );
								}

								return null;
							}, $post_type['taxonomies'] );

							echo implode( ', ', array_filter( $taxonomies ) );
						}
						?>

					</td>
				</tr>

			<?php endforeach; ?>

			</tbody>
		</table>

		<?php
	}

	/**
	 * @return void
	 */
	protected static function taxonomies() {
		$taxonomies = Taxonomies::get_for_display() ?: [];
		ksort( $taxonomies );
		?>

		<h2 class="wp-heading-inline"><?php esc_html_e( 'Taxonomies', 'dt-the7-core' ) ?></h2>
		<a href="<?php echo esc_url( Admin_Page::get_taxonomy_link( Admin_Page::ACTION_NEW ) ) ?>" class="page-title-action"><?php esc_html_e( 'Add new', 'dt-the7-core' ) ?></a>

		<table class="wp-list-table widefat fixed striped table-view-list">
			<thead>
			<tr>
				<th scope="col" class="manage-column column-primary"><?php esc_html_e( 'Title', 'dt-the7-core' ) ?></th>
				<th scope="col" class="manage-column"><?php esc_html_e( 'Post Types', 'dt-the7-core' ) ?></th>
			</tr>
			</thead>

			<tbody>

			<?php foreach ( $taxonomies as $taxonomy ) :
				if ( ! empty( $taxonomy['disabled'] ) ) {
					continue;
				}

				$is_predefined = ! empty( $taxonomy['predefined'] );

				$title = self::get_item_label( $taxonomy );
			?>

				<tr>
					<td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
						<strong><a class="row-title" href="<?php echo esc_url( Admin_Page::get_taxonomy_link( Admin_Page::ACTION_EDIT, $taxonomy['name'] ) ) ?>" aria-label="<?php echo esc_attr( $title ) ?>"><?php echo esc_html( $title ) ?></a></strong>

						<?php
						$actions = [
							'edit' => '<a href="' . esc_url( Admin_Page::get_taxonomy_link( Admin_Page::ACTION_EDIT, $taxonomy['name'] ) ) . '" aria-label="' . esc_attr( sprintf( esc_html__( 'Edit %s', 'dt-the7-core' ), $title ) ) . '">' . esc_html__( 'Edit', 'dt-the7-core' ) . '</a>',
						];

						if ( ! $is_predefined ) {
							$actions['delete'] = '<a href="' . Post_Types_Handler::nonce_url( Admin_Page::get_taxonomy_link( Admin_Page::ACTION_DELETE, $taxonomy['name'] ) ) . '" aria-label="">' . esc_html__( 'Delete', 'dt-the7-core' ) . '</a>';
						}

						echo self::row_actions( $actions );
						?>
					</td>
					<td class="post-types" data-colname="Post Types">

						<?php
						if ( ! empty( $taxonomy['object_types'] ) ) {
							$post_types = array_map( function ( $slug ) {
								$post_type = Post_Types::get_for_display( $slug );
								if ( $post_type ) {
									return sprintf(
										'<a href="%s">%s</a>',
										esc_url( Admin_Page::get_post_type_link( Admin_Page::ACTION_EDIT, $slug ) ),
										esc_html( self::get_item_label( $post_type ) )
									);
								}

								$post_type = current( get_post_types( [ 'name' => $slug, 'public' => true ], 'objects' ) );
								if ( $post_type ) {
									return esc_html( $post_type->label );
								}

								return null;
							}, $taxonomy['object_types'] );

							echo implode( ', ', array_filter( $post_types ) );
						}
						?>

					</td>
				</tr>

			<?php endforeach; ?>

			</tbody>
		</table>

		<?php
	}

	/**
	 * Generates the required HTML for a list of row action links.
	 *
	 * @see WP_List_Table::row_actions.
	 *
	 * @param string[] $actions        An array of action links.
	 * @return string The HTML for the row actions.
	 */
	protected static function row_actions( $actions ) {
		$action_count = count( $actions );

		if ( ! $action_count ) {
			return '';
		}

		$out = '<div class="row-actions">';

		$i = 0;

		foreach ( $actions as $action => $link ) {
			++$i;

			$sep = ( $i < $action_count ) ? ' | ' : '';

			$out .= "<span class='$action'>$link$sep</span>";
		}

		$out .= '</div>';

		$out .= '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( 'Show more details' ) . '</span></button>';

		return $out;
	}

	/**
	 * @param array $item
	 *
	 * @return string
	 */
	protected static function get_item_label( $item ) {
		$title = isset( $item['label'] ) ? $item['label'] : 'no label';
		if ( ! empty( $item['predefined'] ) ) {
			$title = 'The7 ' . $title;
		}

		return $title;
	}
}
