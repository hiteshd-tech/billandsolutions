<?php
/**
 * Gradient overlay template.
 *
 * @package The7pt
 */

defined( 'ABSPATH' ) || exit;

$placeholder_class = '';
if ( ! has_post_thumbnail() ) {
	$placeholder_class = 'overlay-placeholder';
}
?>

<div class="post-thumbnail-wrap">
	<div class="post-thumbnail <?php echo $placeholder_class; ?>">

		<?php
		if ( ! empty( $post_media ) ) {
			echo $post_media;
		}
		?>

	</div>
</div>

<div class="post-entry-content">
	<div class="post-entry-body">
		<?php
		if ( ! empty( $icons_html ) ) {
			echo $icons_html;
		}

		if ( ! empty( $post_title ) ) {
			echo $post_title;
		}

		if ( ! empty( $post_meta ) ) {
			echo $post_meta;
		}

		if ( ! empty( $post_excerpt ) ) {
			echo '<div class="entry-excerpt">';
			echo $post_excerpt;
			echo '</div>';
		}

		if ( ! empty( $details_btn ) ) {
			echo $details_btn;
		}
		?>
	</div>
</div>