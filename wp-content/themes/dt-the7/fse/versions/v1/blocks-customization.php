<?php
/**
 * Various block customizations. Launched on 'init' hook.
 */

defined( 'ABSPATH' ) || exit;

register_block_pattern_category(
	'dt-the7_page',
	[
		'label'       => _x( 'Pages', 'Block pattern category', 'the7mk2' ),
		'description' => _x( 'A collection of full page layouts. ', 'Block pattern category', 'the7mk2' ),
	]
);

register_block_style(
	'core/button',
	[
		'name'  => 'style-6',
		'label' => __( 'Links', 'the7mk2' ),
	]
);
register_block_style(
	'core/button',
	[
		'name'  => 'style-1',
		'label' => __( 'XS', 'the7mk2' ),
	]
);
register_block_style(
	'core/button',
	[
		'name'  => 'style-2',
		'label' => __( 'S', 'the7mk2' ),
	]
);
register_block_style(
	'core/button',
	[
		'name'  => 'style-3',
		'label' => __( 'M', 'the7mk2' ),
	]
);
register_block_style(
	'core/button',
	[
		'name'  => 'style-4',
		'label' => __( 'L', 'the7mk2' ),
	]
);
register_block_style(
	'core/button',
	[
		'name'  => 'style-5',
		'label' => __( 'XL', 'the7mk2' ),
	]
);

register_block_style(
	'core/navigation',
	[
		'name'         => 'default',
		'label'        => __( 'Default', 'the7mk2' ),
		'is_default'   => true,
		'inline_style' => '
					.wp-block-navigation {
						--wp-navigation-submenu-gap: 5px;
					}
					.wp-block-navigation .wp-block-navigation__submenu-container {
						margin-top: var(--wp-navigation-submenu-gap);
					}
					.wp-block-navigation .wp-block-navigation__submenu-container:before {
						content: "";
						height: var(--wp-navigation-submenu-gap);
						width: 100%;
						position: absolute;
						top: calc(-1px - var(--wp-navigation-submenu-gap));
						left: 0;
					}
					.wp-block-navigation:has(.is-menu-open) .wp-block-navigation__submenu-container {
						margin-top: 0;
					}
					.wp-block-navigation:has(.is-menu-open) .wp-block-navigation__submenu-container:before {
						content: none;
					}

					.wp-block-navigation.is-style-underline,
					.wp-block-navigation.is-style-elastic {
						--wp-navigation-submenu-gap: 10px;
					}
					.wp-block-navigation.is-style-underline .wp-block-navigation-item__content:hover,
					.wp-block-navigation.is-style-underline .is-menu-open .wp-block-navigation-item__content:hover,
					.wp-block-navigation.is-style-elastic .wp-block-navigation-item__content:hover,
					.wp-block-navigation.is-style-elastic .is-menu-open .wp-block-navigation-item__content:hover {
						color: inherit !important;
					}
					.wp-block-navigation.is-style-underline.has-hover .wp-block-navigation__submenu-container .wp-block-navigation-item__content:hover,
					.wp-block-navigation.is-style-elastic.has-hover .wp-block-navigation__submenu-container .wp-block-navigation-item__content:hover {
						color: var(--wp-navigation-hover, initial) !important;
					}
					.wp-block-navigation.is-style-underline.has-submenu-hover .wp-block-navigation__submenu-container .wp-block-navigation-item__content:hover,
					.wp-block-navigation.is-style-elastic.has-submenu-hover .wp-block-navigation__submenu-container .wp-block-navigation-item__content:hover {
						color: var(--wp-navigation-submenu-hover, initial) !important;
					}
				',
	]
);

register_block_style(
	'core/navigation',
	[
		'name'         => 'underline',
		'label'        => __( 'Underline', 'the7mk2' ),
		'inline_style' => '
					.wp-block-navigation.is-style-underline .wp-block-navigation-item__content {
						position: relative;
					}
					.wp-block-navigation.is-style-underline .wp-block-navigation-item__content:after {
						content: "";
						position: absolute;
						width: auto;
						height: 2px;
						background: var(--wp-navigation-hover, currentColor);
						left: 0;
						right: 0;
						bottom: -2px;
						opacity: 0;
						transition: opacity .1s;
					}
					.wp-block-navigation.is-style-underline .wp-block-navigation-item__content:hover:after {
						opacity: 1;
					}
					.wp-block-navigation.is-style-underline.has-submenu-hover  .is-menu-open .wp-block-navigation-item__content:after {
    					background: var(--wp-navigation-submenu-hover, currentColor);
					}
					.wp-block-navigation.is-style-underline .wp-block-navigation__submenu-container .wp-block-navigation-item__content.wp-block-navigation-item__content:after {
						content: none;
					}
				',
	]
);

register_block_style(
	'core/navigation',
	[
		'name'         => 'elastic',
		'label'        => __( 'Elastic', 'the7mk2' ),
		'inline_style' => '
					.wp-block-navigation.is-style-elastic .wp-block-navigation-item__content {
						position: relative;
					}
					.wp-block-navigation.is-style-elastic .wp-block-navigation-item__content:after {
						content: "";
						position: absolute;
						width: auto;
						height: 2px;
						background: var(--wp-navigation-hover, currentColor);
						left: 50%;
						right: 50%;
						bottom: -2px;
						opacity: 0;
						translate3d(0, 0, 0);
						transition: left .3s cubic-bezier(.175,.885,.32,1.275), right .3s cubic-bezier(.175,.885,.32,1.275), opacity .3s ease;
					}
					.wp-block-navigation.is-style-elastic .wp-block-navigation-item__content:hover:after {
						left: 0;
						right: 0;
						opacity: 1;
					}
					.wp-block-navigation.is-style-elastic.has-submenu-hover  .is-menu-open .wp-block-navigation-item__content:after {
    					background: var(--wp-navigation-submenu-hover, currentColor);
					}
					.wp-block-navigation.is-style-elastic .wp-block-navigation__submenu-container .wp-block-navigation-item__content.wp-block-navigation-item__content:after {
						content: none;
					}

					.wp-block-navigation.is-style-elastic .wp-block-navigation__submenu-container,
					.wp-block-navigation.is-style-elastic .wp-block-navigation__submenu-container .wp-block-navigation-item__content {
						transition-duration: .2s !important;
					}
				',
	]
);
