<?php
/**
 * Testimonial post template.
 */

// ! File Security Check
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

$post_id     = get_the_ID();
$config      = presscore_config();
$show_avatar = $config->get( 'show_avatar', true );
$avatar      = '';
if ( $show_avatar ) {
	$avatar = '<span class="alignleft no-avatar"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 16 16" style="enable-background:new 0 0 16 16;" xml:space="preserve"><path d="M8,8c2.2,0,4-1.8,4-4s-1.8-4-4-4S4,1.8,4,4S5.8,8,8,8z M8,10c-2.7,0-8,1.3-8,4v1c0,0.5,0.4,1,1,1h14c0.5,0,1-0.5,1-1v-1
	C16,11.3,10.7,10,8,10z"/></svg></span>';

	if ( has_post_thumbnail( $post_id ) ) {
		$avatar = dt_get_thumb_img(
			array(
				'img_id'  => get_post_thumbnail_id( $post_id ),
				'options' => array( 'w' => 60, 'h' => 60 ),
				'echo'    => false,
				'wrap'    => '<img %IMG_CLASS% %SRC% %SIZE% %IMG_TITLE% %ALT% />',
			)
		);

		$avatar_wrap_class = 'alignleft';
		if ( presscore_lazy_loading_enabled() ) {
			$avatar_wrap_class .= ' layzr-bg';
		}

		$avatar = '<span class="' . $avatar_wrap_class . '">' . $avatar . '</span>';
	}
}

$position = get_post_meta( $post_id, '_dt_testimonial_options_position', true );
if ( $position ) {
	$position = '<span class="text-secondary color-secondary">' . wp_kses_post( $position ) . '</span>';
}

$title = get_the_title();
if ( $title ) {
	$title = '<span class="text-primary">' . esc_html( $title ) . '</span>';
}

$details_link = '';
if ( get_post_meta( $post_id, '_dt_testimonial_options_go_to_single', true ) ) {
	$details_link = ' ' . presscore_post_details_link( null, array( 'more-link' ), esc_html__( 'read more', 'dt-the7-core' ) );
}
?>
<article>
	<div class="testimonial-content">
		<?php
		if ( $post->post_excerpt ) {
			echo apply_filters( 'the_excerpt', get_the_excerpt() . $details_link );
		} else {
			echo apply_filters( 'the_content', get_the_content() . $details_link );
		}
		?>
	</div>
	<div class="testimonial-vcard">
		<div class="testimonial-thumbnail">
			<?php echo $avatar ?>
		</div>
		<div class="testimonial-desc">
			<?php echo $title . $position ?>
		</div>
	</div>
</article>
