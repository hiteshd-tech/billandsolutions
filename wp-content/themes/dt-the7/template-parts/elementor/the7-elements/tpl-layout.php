<?php
/**
 * Classic layout.
 *
 * @package The7pt
 */

defined( 'ABSPATH' ) || exit;

?>

<?php if ( ! empty( $post_media ) ) : ?>

	<div class="post-thumbnail-wrap">
		<div class="post-thumbnail">

			<?php
			echo $post_media;

			if ( ! empty( $icons_html ) ) {
				echo $icons_html;
			}
			?>

		</div>
	</div>

<?php endif; ?>

<div class="post-entry-content">

	<?php
	if ( ! empty( $post_title ) ) {
		echo $post_title;
	}

	if ( isset( $post_meta ) ) {
		echo $post_meta;
	}

	if ( ! empty( $post_excerpt ) ) {
		echo '<div class="entry-excerpt">';
		echo $post_excerpt;
		echo '</div>';
	}

	if ( isset( $details_btn ) ) {
		echo $details_btn;
	}
	?>

</div>