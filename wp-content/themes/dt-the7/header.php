<?php
/**
 * Header template.
 *
 * @since   1.0.0
 *
 * @package The7\Templates
 */

defined( 'ABSPATH' ) || exit;

if ( ! the7_is_gutenberg_theme_mode_active() ) {
	get_template_part( 'header-single' );
	get_template_part( 'header-main' );
}

// Little trick!
// wp_head()
