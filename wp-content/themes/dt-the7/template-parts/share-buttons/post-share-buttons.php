<?php
/**
 * Template for post share buttons.
 *
 * @since   7.8.0
 *
 * @package The7
 *
 * @var string $wrap_class
 * @var string $share_buttons_header
 * @var array[] $share_buttons
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="<?php echo esc_attr( $wrap_class ); ?>">
	<div class="share-link-description"><span class="share-link-icon"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 16 16" style="enable-background:new 0 0 16 16;" xml:space="preserve"><path d="M11,2.5C11,1.1,12.1,0,13.5,0S16,1.1,16,2.5C16,3.9,14.9,5,13.5,5c-0.7,0-1.4-0.3-1.9-0.9L4.9,7.2c0.2,0.5,0.2,1,0,1.5l6.7,3.1c0.9-1,2.5-1.2,3.5-0.3s1.2,2.5,0.3,3.5s-2.5,1.2-3.5,0.3c-0.8-0.7-1.1-1.7-0.8-2.6L4.4,9.6c-0.9,1-2.5,1.2-3.5,0.3s-1.2-2.5-0.3-3.5s2.5-1.2,3.5-0.3c0.1,0.1,0.2,0.2,0.3,0.3l6.7-3.1C11,3,11,2.8,11,2.5z"/></svg></span><?php echo esc_html( $share_buttons_header ); ?></div>
	<div class="share-buttons">
		<?php
		foreach ( $share_buttons as $share_button ) {
			$share_button = wp_parse_args(
				$share_button,
				[
					'icon_class'  => '',
					'url'         => '',
					'name'        => '',
					'custom_atts' => '',
					'alt_title'   => '',
					'title'       => '',
					'svg_icon'    => '',
				]
			);

			printf(
				'<a class="%1$s" href="%2$s" title="%3$s" target="_blank" %4$s>%7$s<span class="soc-font-icon"></span><span class="social-text">%5$s</span><span class="screen-reader-text">%6$s</span></a>' . "\n",
				esc_attr( $share_button['icon_class'] ),
				esc_url( $share_button['url'] ),
				esc_attr( $share_button['name'] ),
				$share_button['custom_atts'],
				esc_html( $share_button['alt_title'] ),
				esc_html( $share_button['title'] ),
				$share_button['svg_icon']
			);
		}
		?>
	</div>
</div>
