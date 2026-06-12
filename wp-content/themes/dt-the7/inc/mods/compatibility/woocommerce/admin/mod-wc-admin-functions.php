<?php
/**
 * Admint functions for WC module.
 *
 * @package vogue
 * @since 1.0.0
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'dt_woocommerce_add_theme_options_page' ) ) :

	/**
	 * Add WooCommerce theme options page.
	 *
	 * @param  array  $menu_items
	 * @return array
	 */
	function dt_woocommerce_add_theme_options_page( $menu_items = array() ) {
		$menu_slug = 'of-woocommerce-menu';
		if ( ! array_key_exists( $menu_slug, $menu_items ) ) {
			$menu_items[ $menu_slug ] = array(
				'menu_title'       => _x( 'WooCommerce', 'backend', 'the7mk2' ),
			);
		}
		return $menu_items;
	}

	add_filter( 'presscore_options_menu_config', 'dt_woocommerce_add_theme_options_page', 20 );

endif;

if ( ! function_exists( 'dt_woocommerce_add_theme_options' ) ) {

	function dt_woocommerce_add_theme_options( $files_list ) {
		$menu_slug = 'of-woocommerce-menu';
		if ( ! array_key_exists( $menu_slug, $files_list ) ) {
			$files_list[ $menu_slug ] = plugin_dir_path( __FILE__ ) . 'mod-wc-options.php';
		}
		return $files_list;
	}
	add_filter( 'presscore_options_files_list', 'dt_woocommerce_add_theme_options' );

}

if ( ! function_exists( 'dt_woocommerce_inject_theme_options' ) ) :

	function dt_woocommerce_inject_theme_options( $options ) {
		if ( array_key_exists( 'of-header-menu', $options ) ) {
			$options['of-woocommerce-mod-injected-header-options'] = plugin_dir_path( __FILE__ ) . 'wc-cart-micro-widget-options.php';
		}
		if ( array_key_exists( 'of-likebuttons-menu', $options ) ) {
			$options[] = plugin_dir_path( __FILE__ ) . 'wc-share-buttons-options.php';
		}
		return $options;
	}
	add_filter( 'presscore_options_files_to_load', 'dt_woocommerce_inject_theme_options' );

endif;

if ( ! function_exists( 'dt_woocommerce_setup_less_vars' ) ) :

	/**
	 * @param The7_Less_Vars_Manager_Interface $less_vars
	 */
	function dt_woocommerce_setup_less_vars( The7_Less_Vars_Manager_Interface $less_vars ) {
		the7_less_add_responsive_font($less_vars,"header-elements-woocommerce_cart-font-content", "product-microwidget-content");

		$less_vars->add_pixel_number( 'product-title-gap', of_get_option( 'woocommerce_product_title_gap' ) );
		$less_vars->add_pixel_number( 'product-price-gap', of_get_option( 'woocommerce_product_price_gap' ) );
		$less_vars->add_pixel_number( 'product-rating-gap', of_get_option( 'woocommerce_product_rating_gap' ) );
		$less_vars->add_pixel_number( 'product-description-gap', of_get_option( 'woocommerce_product_desc_gap' ) );
		$less_vars->add_pixel_number( 'product-cart-gap', of_get_option( 'woocommerce_product_cart_gap' ) );
		$less_vars->add_keyword( 'product-alignment', of_get_option( 'woocommerce_display_align' ) );

		$less_vars->add_hex_color(
			'product-counter-color',
			of_get_option( 'header-elements-woocommerce_cart-counter-color' )
		);

		$counter_color_vars = array( 'product-counter-bg', 'product-counter-bg-2' );
		switch ( of_get_option( 'header-elements-woocommerce_cart-counter-bg' ) ) {
			case 'color':
				$less_vars->add_rgba_color( $counter_color_vars, array( of_get_option( 'header-elements-woocommerce_cart-counter-bg-color' ), null ) );
				break;
			case 'gradient':
				$gradient_obj = the7_less_create_gradient_obj( of_get_option( 'header-elements-woocommerce_cart-counter-bg-gradient' ) );
				$less_vars->add_rgba_color( $counter_color_vars[0], $gradient_obj->get_color_stop( 1 )->get_color() );
				$less_vars->add_keyword( $counter_color_vars[1], $gradient_obj->with_angle( 'left' )->get_string() );
				break;
			case 'accent':
			default:
				list( $first_color, $gradient ) = the7_less_get_accent_colors( $less_vars );
				$less_vars->add_rgba_color( $counter_color_vars[0], $first_color );
				$less_vars->add_keyword( $counter_color_vars[1], $gradient->with_angle( 'left' )->get_string() );
		}
		unset( $gradient_obj, $first_color, $gradient, $counter_color_vars );

		$less_vars->add_hex_color(
			'sub-cart-color',
			of_get_option( 'header-elements-woocommerce_cart-sub_cart-font-color' )
		);

		$less_vars->add_pixel_number(
			'sub-cart-width',
			of_get_option( 'header-elements-woocommerce_cart-sub_cart-bg-width' )
		);
		$less_vars->add_rgba_color(
			'sub-cart-bg',
			of_get_option( 'header-elements-woocommerce_cart-sub_cart-bg-color' )
		);

		$less_vars->add_number(
			'product-img-width',
			of_get_option( 'woocommerce_product_img_width' )
		);
		$less_vars->add_number(
			'cart-total-width',
			of_get_option( 'woocommerce_cart_total_width' )
		);
		$less_vars->storage()->start_excluding_css_vars();
		$less_vars->add_pixel_number(
			'switch-cart-list-to-mobile',
			of_get_option( 'woocommerce_cart_switch' )
		);
		$less_vars->add_pixel_number(
			'switch-product-to-mobile',
			of_get_option( 'woocommerce_product_switch' )
		);
		$less_vars->add_pixel_number(
			'wc-list-switch-to-mobile',
			of_get_option( 'woocommerce_list_switch' )
		);
		$less_vars->storage()->end_excluding_css_vars();
		$less_vars->add_rgba_color(
			'wc-steps-bg',
			of_get_option( 'woocommerce_steps_bg_color' ),
			of_get_option( 'woocommerce_steps-bg_opacity' )
		);
		$less_vars->add_hex_color(
			'wc-steps-color',
			of_get_option( 'woocommerce_steps_color', '#000000' )
		);
		$less_vars->add_paddings( array(
				'wc-step-padding-top',
				'wc-step-padding-bottom',
		), of_get_option( 'woocommerce_cart_padding' ) );
		$less_vars->add_number(
			'wc-list-img-width',
			of_get_option( 'woocommerce_shop_template_img_width' )
		);

	}
	add_action( 'presscore_setup_less_vars', 'dt_woocommerce_setup_less_vars', 20 );

endif;

if ( ! function_exists( 'dt_woocommerce_add_product_metaboxes' ) ) :

	/**
	 * Add common meta boxes to product post type.
	 */
	function dt_woocommerce_add_product_metaboxes( $pages ) {
		$pages[] = 'product';
		return $pages;
	}

	add_filter( 'presscore_pages_with_basic_meta_boxes', 'dt_woocommerce_add_product_metaboxes' );

endif;

if ( ! function_exists( 'dt_woocommerce_add_cart_micro_widget_filter' ) ) {

	/**
	 * This filter add cart micro widget to header options.
	 *
	 * @since 5.5.0
	 *
	 * @param array $elements
	 *
	 * @return array
	 */
	function dt_woocommerce_add_cart_micro_widget_filter( $elements = array() ) {
		$elements['cart'] = array( 'title' => _x( 'Cart', 'theme-options', 'the7mk2' ), 'class' => '' );

		return $elements;
	}

	add_filter( 'header_layout_elements', 'dt_woocommerce_add_cart_micro_widget_filter' );
}

/**
 * Shortcodes inline css generated on post save, no need to duplicate it.
 *
 * @see the7_save_shortcode_inline_css
 *
 * @param array $exclude_meta
 *
 * @return array
 */
function the7_prevent_the7_shortcodes_dynamic_css_meta_duplication_with_product_duplication( $exclude_meta ) {
	$exclude_meta[] = 'the7_shortcodes_dynamic_css';

	return $exclude_meta;
}
add_filter( 'woocommerce_duplicate_product_exclude_meta', 'the7_prevent_the7_shortcodes_dynamic_css_meta_duplication_with_product_duplication' );

/**
 * Add sidebar columns to products on manage_edit page.
 */
add_filter( 'manage_edit-product_columns', 'presscore_admin_add_sidebars_columns' );

/**
 * Add shortcodes.
 */
add_action( 'init', array( 'DT_WC_Shortcodes', 'init' ) );

// Swatch type.
add_filter( 'product_attributes_type_selector', 'the7_wc_extended_attribute_types' );

/**
 * @param array $attribute_types Array of attribute types.
 *
 * @return array
 */
function the7_wc_extended_attribute_types( $attribute_types ) {
	$attribute_types += the7_wc_get_extended_attribute_types();

	return $attribute_types;
}

/**
 * @return array
 */
function the7_wc_get_extended_attribute_types() {
	return [
		'the7_echanced' => esc_html__( 'The7 swatches', 'the7mk2' ),
	];
}

add_action( 'admin_init', 'the7_wc_attribute_meta_fields' );

/**
 * @return void
 */
function the7_wc_attribute_meta_fields() {
	$attribute_taxonomies = wc_get_attribute_taxonomies();

	if ( ! $attribute_taxonomies ) {
		return;
	}

	require_once PRESSCORE_DIR . '/vendor/Tax-meta-class/Tax-meta-class.php';

	$the7_attribute_types = array_fill_keys( array_keys( the7_wc_get_extended_attribute_types() ), [] );

	foreach ( $attribute_taxonomies as $taxonomy ) {
		$attribute_name = wc_attribute_taxonomy_name( $taxonomy->attribute_name );
		$attribute_type = $taxonomy->attribute_type;
		if ( array_key_exists( $attribute_type, $the7_attribute_types ) ) {
			$the7_attribute_types[ $attribute_type ][] = $attribute_name;
		}
	}

	foreach ( $the7_attribute_types as $type => $pages ) {
		if ( empty( $pages ) ) {
			continue;
		}

		// Configure meta boxes.
		$config = [
			'id'             => 'the7_wc_attribute_type_' . $type,
			'pages'          => $pages,
			'context'        => 'normal',
			'fields'         => [],
			'local_images'   => true,
			'use_with_theme' => PRESSCORE_URI . '/vendor/Tax-meta-class',
		];

		// Print inline js script to switch between attribute types. Add it only on supported pages.
		add_action( 'admin_print_footer_scripts-term.php', function () use ( $pages ) {
			the7_wc_maybe_render_custom_attribute_terms_js( $pages );
		} );
		add_action( 'admin_print_footer_scripts-edit-tags.php', function () use ( $pages ) {
			the7_wc_maybe_render_custom_attribute_terms_js( $pages );
		} );

		// Init meta boxes.
		$meta_box = new Tax_Meta_Class( $config );

		$meta_box->addSelect(
			'the7_attribute_type',
			[
				'color' => esc_html_x( 'Color', 'backend', 'the7mk2' ),
				'image' => esc_html_x( 'Image', 'backend', 'the7mk2' ),
			],
			[
				'name'  => esc_html_x( 'Type', 'backend', 'the7mk2' ),
				'std'   => 'color',
				'style' => 'line-height: initial;',
			]
		);

		$meta_box->addColor(
			'the7_attribute_type_color',
			[
				'name' => esc_html_x( 'Color', 'backend', 'the7mk2' ),
			]
		);

		$meta_box->addImage(
			'the7_attribute_type_image',
			[
				'name'  => esc_html_x( 'Image', 'backend', 'the7mk2' ),
				'width' => '100px',
			]
		);

		// Finish meta mox declaration.
		$meta_box->Finish();

		if ( did_action( 'admin_init' ) || doing_action( 'admin_init' ) ) {
			$meta_box->add();
		}
	}
}

// Render custom attribute terms in edit context.
add_action( 'woocommerce_product_option_terms', 'the7_wc_render_custom_attribute_terms', 10, 3 );

/**
 * @param  array|null  $attribute_taxonomy  Attribute taxonomy object.
 * @param  number  $i  Attribute index.
 * @param  WC_Product_Attribute  $attribute  Attribute object.
 *
 * @return void
 */
function the7_wc_render_custom_attribute_terms( $attribute_taxonomy, $i, $attribute ) {
	// Skip for non-extended attributes.
	if ( ! array_key_exists( $attribute_taxonomy->attribute_type, the7_wc_get_extended_attribute_types() ) ) {
		return;
	}

	$attribute_orderby = ! empty( $attribute_taxonomy->attribute_orderby ) ? $attribute_taxonomy->attribute_orderby : 'name';
	?>
	<select multiple="multiple"
			data-minimum_input_length="0"
			data-limit="50" data-return_id="id"
			data-placeholder="<?php esc_attr_e( 'Select terms', 'woocommerce' ); ?>"
			data-orderby="<?php echo esc_attr( $attribute_orderby ); ?>"
			class="multiselect attribute_values wc-taxonomy-term-search"
			name="attribute_values[<?php echo esc_attr( $i ); ?>][]"
			data-taxonomy="<?php echo esc_attr( $attribute->get_taxonomy() ); ?>"
	>
		<?php
		$selected_terms = $attribute->get_terms();
		if ( $selected_terms ) {
			foreach ( $selected_terms as $selected_term ) {
				echo '<option value="' . esc_attr( $selected_term->term_id ) . '" selected="selected">' . esc_html( apply_filters( 'woocommerce_product_attribute_term_name', $selected_term->name, $selected_term ) ) . '</option>';
			}
		}
		?>
	</select>
	<button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'woocommerce' ); ?></button>
	<button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'woocommerce' ); ?></button>
	<?php
}

/**
 * @param array $supported_taxonomies Supported taxonomies.
 *
 * @return void
 */
function the7_wc_maybe_render_custom_attribute_terms_js( $supported_taxonomies = [] ) {
	$screen = get_current_screen();

	if ( ! $screen || ! in_array( $screen->taxonomy, $supported_taxonomies, true ) ) {
		return;
	}
	?>
	<script>
        jQuery(function ($) {
            $('.at-select[name="the7_attribute_type"]').on('change', function () {
                var $this = $(this);
                var $color = $('.at-color[name="the7_attribute_type_color"]');
                var $image = $('#the7_attribute_type_image');
                if ($this.val() === 'image') {
                    $color.closest('.form-field').hide();
                    $image.closest('.form-field').show();
                } else {
                    $color.closest('.form-field').show();
                    $image.closest('.form-field').hide();
                }
            }).trigger('change');
        });
	</script>
	<?php
}
