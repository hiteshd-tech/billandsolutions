<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the <div class="wf-container"> and all content after
 *
 * @since 1.0.0
 *
 * @package The7\Templates
 */

defined( 'ABSPATH' ) || exit;

if ( presscore_is_content_visible() ) : ?>

			</div><!-- .wf-container -->
		</div><!-- .wf-wrap -->

	<?php
	/**
	 * Do something after content container close tag.
	 *
	 * @since 6.8.1
	 */
	do_action( 'the7_after_content_container' );
	?>

	</div><!-- #main -->

	<?php
	if ( presscore_config()->get( 'template.footer.background.slideout_mode' ) ) {
		echo '</div>';
	}
	?>

<?php endif; ?>

<?php do_action( 'presscore_after_main_container' ); ?>
<?php ob_start(); ?>
<a href="#" class="scroll-top"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 16 16" style="enable-background:new 0 0 16 16;" xml:space="preserve">
<path d="M11.7,6.3l-3-3C8.5,3.1,8.3,3,8,3c0,0,0,0,0,0C7.7,3,7.5,3.1,7.3,3.3l-3,3c-0.4,0.4-0.4,1,0,1.4c0.4,0.4,1,0.4,1.4,0L7,6.4
	V12c0,0.6,0.4,1,1,1s1-0.4,1-1V6.4l1.3,1.3c0.4,0.4,1,0.4,1.4,0C11.9,7.5,12,7.3,12,7S11.9,6.5,11.7,6.3z"/>
</svg><span class="screen-reader-text"><?php esc_html_e( 'Go to Top', 'the7mk2' ); ?></span></a>
<?php  echo apply_filters( 'presscore_scroll_top_html', ob_get_clean()); ?>

</div><!-- #page -->

<?php

wp_footer(); ?>

<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="pswp__bg"></div>
	<div class="pswp__scroll-wrap">
		<div class="pswp__container">
			<div class="pswp__item"></div>
			<div class="pswp__item"></div>
			<div class="pswp__item"></div>
		</div>
		<div class="pswp__ui pswp__ui--hidden">
			<div class="pswp__top-bar">
				<div class="pswp__counter"></div>
				<button class="pswp__button pswp__button--close" title="<?php esc_html_e( 'Close (Esc)', 'the7mk2' ) ?>" aria-label="<?php esc_html_e( 'Close (Esc)', 'the7mk2' ) ?>"></button>
				<button class="pswp__button pswp__button--share" title="<?php esc_html_e( 'Share', 'the7mk2' ) ?>" aria-label="<?php esc_html_e( 'Share', 'the7mk2' ) ?>"></button>
				<button class="pswp__button pswp__button--fs" title="<?php esc_html_e( 'Toggle fullscreen', 'the7mk2' ) ?>" aria-label="<?php esc_html_e( 'Toggle fullscreen', 'the7mk2' ) ?>"></button>
				<button class="pswp__button pswp__button--zoom" title="<?php esc_html_e( 'Zoom in/out', 'the7mk2' ) ?>" aria-label="<?php esc_html_e( 'Zoom in/out', 'the7mk2' ) ?>"></button>
				<div class="pswp__preloader">
					<div class="pswp__preloader__icn">
						<div class="pswp__preloader__cut">
							<div class="pswp__preloader__donut"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
				<div class="pswp__share-tooltip"></div> 
			</div>
			<button class="pswp__button pswp__button--arrow--left" title="<?php esc_html_e( 'Previous (arrow left)', 'the7mk2' ) ?>" aria-label="<?php esc_html_e( 'Previous (arrow left)', 'the7mk2' ) ?>">
			</button>
			<button class="pswp__button pswp__button--arrow--right" title="<?php esc_html_e( 'Next (arrow right)', 'the7mk2' ) ?>" aria-label="<?php esc_html_e( 'Next (arrow right)', 'the7mk2' ) ?>">
			</button>
			<div class="pswp__caption">
				<div class="pswp__caption__center"></div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
