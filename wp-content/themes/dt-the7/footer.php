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

if ( ! the7_is_gutenberg_theme_mode_active() ) {
	get_template_part( 'footer-main' );
}
