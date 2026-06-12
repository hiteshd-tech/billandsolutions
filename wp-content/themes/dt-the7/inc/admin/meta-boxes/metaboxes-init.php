<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

global $DT_META_BOXES;
$DT_META_BOXES = [];

$yes_no_options = array(
	'1'	=> _x('Yes', 'backend metabox', 'the7mk2'),
	'0' => _x('No', 'backend metabox', 'the7mk2'),
);

// Ordering settings
$order_options = array(
	'ASC'	=> _x('ascending', 'backend', 'the7mk2'),
	'DESC'	=> _x('descending', 'backend', 'the7mk2'),
);

$orderby_options = array(
	'ID'			=> _x('ID', 'backend', 'the7mk2'),
	'author'		=> _x('author', 'backend', 'the7mk2'),
	'title'			=> _x('title', 'backend', 'the7mk2'),
	'date'			=> _x('date', 'backend', 'the7mk2'),
	'name'			=> _x('name', 'backend', 'the7mk2'),
	'modified'		=> _x('modified', 'backend', 'the7mk2'),
	'parent'		=> _x('parent', 'backend', 'the7mk2'),
	'rand'			=> _x('rand', 'backend', 'the7mk2'),
	'comment_count'	=> _x('comment_count', 'backend', 'the7mk2'),
	'menu_order'	=> _x('menu_order', 'backend', 'the7mk2'),
);

// Get widgetareas
$widgetareas_list = presscore_get_widgetareas_options();
if ( !$widgetareas_list ) {
	$widgetareas_list = array('none' => _x('None', 'backend metabox', 'the7mk2'));
}

$enabled_disabled = array(
	'1'	=> _x('Enabled', 'backend metabox', 'the7mk2'),
	'0' => _x('Disabled', 'backend metabox', 'the7mk2'),
);

// Image settings
$repeat_options = array(
	'repeat'	=> _x('repeat', 'backend', 'the7mk2'),
	'repeat-x'	=> _x('repeat-x', 'backend', 'the7mk2'),
	'repeat-y'	=> _x('repeat-y', 'backend', 'the7mk2'),
	'no-repeat'	=> _x('no-repeat', 'backend', 'the7mk2'),
);

$position_x_options = array(
	'center'	=> _x('center', 'backend', 'the7mk2'),
	'left'		=> _x('left', 'backend', 'the7mk2'),
	'right'		=> _x('right', 'backend', 'the7mk2'),
);

$position_y_options = array(
	'center'	=> _x('center', 'backend', 'the7mk2'),
	'top'		=> _x('top', 'backend', 'the7mk2'),
	'bottom'	=> _x('bottom', 'backend', 'the7mk2'),
);

$load_style_options = array(
	'ajax_pagination'	=> _x('Pagination & filter with AJAX', 'backend metabox', 'the7mk2'),
	'ajax_more'			=> _x('"Load more" button & filter with AJAX', 'backend metabox', 'the7mk2'),
	'lazy_loading'		=> _x('Lazy loading', 'backend metabox', 'the7mk2'),
	'default'			=> _x('Standard (no AJAX)', 'backend metabox', 'the7mk2')
);

$font_size = array(
	'h1'		=> _x('h1', 'backend metabox', 'the7mk2'),
	'h2'		=> _x('h2', 'backend metabox', 'the7mk2'),
	'h3'		=> _x('h3', 'backend metabox', 'the7mk2'),
	'h4'		=> _x('h4', 'backend metabox', 'the7mk2'),
	'h5'		=> _x('h5', 'backend metabox', 'the7mk2'),
	'h6'		=> _x('h6', 'backend metabox', 'the7mk2'),
	'small'		=> _x('small', 'backend metabox', 'the7mk2'),
	'normal'	=> _x('medium', 'backend metabox', 'the7mk2'),
	'big'		=> _x('large', 'backend metabox', 'the7mk2')
);

$accent_custom_color = array(
	'accent'	=> _x('Accent', 'backend metabox', 'the7mk2'),
	'color'		=> _x('Custom color', 'backend metabox', 'the7mk2')
);

/**
 * Get advanced settings open block.
 *
 * @return string.
 */
function presscore_meta_boxes_advanced_settings_tpl( $id = 'dt-advanced' ) {
	return sprintf(
		'<div class="hide-if-no-js"><div class="dt_hr"></div><p><a href="#advanced-options" class="dt_advanced">
				<input type="hidden" name="%1$s" data-name="%1$s" value="hide" />
				<span class="dt_advanced-show">%2$s</span>
				<span class="dt_advanced-hide">%3$s</span> 
				%4$s
			</a></p></div><div class="%1$s dt_container hide-if-js"><div class="dt_hr"></div>',
		esc_attr(''.$id),
		_x('+ Show', 'backend metabox', 'the7mk2'),
		_x('- Hide', 'backend metabox', 'the7mk2'),
		_x('advanced settings', 'backend metabox', 'the7mk2')
	);
}

