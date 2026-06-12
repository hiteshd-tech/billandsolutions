<?php

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Load only while debugging.
if ( ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
	return;
}

/**
 * Shortcode for testing custom Visual Composer params.
 */
class DT_Shortcode_Testing_Params extends DT_Shortcode {

	/**
	 * @var DT_Shortcode_Testing_Params
	 */
	protected static $instance;

	/**
	 * @var string
	 */
	protected $shortcode_name = 'dt_testing_params';

	/**
	 * Bootstrap shortcode.
	 *
	 * @return DT_Shortcode_Testing_Params
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register shortcode.
	 */
	protected function __construct() {
		add_shortcode( $this->shortcode_name, array( $this, 'shortcode' ) );
	}

	/**
	 * Render shortcode output.
	 *
	 * @param array       $atts
	 * @param string|null $content
	 *
	 * @return string
	 */
	public function shortcode( $atts, $content = null ) {
		$default_atts = array(
			'debug_taxonomy'          => '',
			'debug_post_selection'    => '',
			'debug_spacing'           => '10px 15px 10px 15px',
			'debug_columns'           => 'desktop:4|h_tablet:3|v_tablet:2|phone:1',
			'debug_dimensions'        => '320x180',
			'debug_number'            => '24px',
			'debug_number_icon'       => '3px',
			'debug_font_style'        => 'italic:bold:uppercase',
			'debug_switch'            => 'y',
			'debug_navigation_icon'   => 'icon-ar-017-r',
			'debug_icon_manager'      => '',
			'debug_soc_icon_manager'  => 'dt-icon-facebook',
			'debug_radio_image'       => 'layout_1',
			'debug_gradient'          => '90deg|rgba(12,239,154,0.8) 0%|rgba(0,108,220,0.8) 50%|rgba(184,38,220,0.8) 100%',
			'debug_autocomplete'      => '',
		);

		$atts = shortcode_atts( $default_atts, $atts, $this->shortcode_name );

		$rows = '';
		foreach ( $atts as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = implode( ', ', $value );
			}

			$rows .= sprintf(
				'<tr><th scope="row">%1$s</th><td><code>%2$s</code></td></tr>',
				esc_html( $key ),
				esc_html( (string) $value )
			);
		}

		if ( ! $rows ) {
			return '';
		}

		return '<div class="dt-testing-params"><h4>' . esc_html__( 'The7 Params Debug', 'the7mk2' ) . '</h4><table class="dt-testing-params__table">' . $rows . '</table></div>';
	}
}

DT_Shortcode_Testing_Params::get_instance();
