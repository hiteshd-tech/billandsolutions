<?php
/**
 * The7 Product Counter widget.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Class Products_Counter
 *
 * @package The7\Mods\Compatibility\Elementor\Widgets\Woocommerce
 */
class Products_Counter extends The7_Elementor_Widget_Base {

	/**
	 * Get element name.
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-products-counter';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	protected function the7_title() {
		return esc_html__( 'Product Counter', 'the7mk2' );
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'woocommerce', 'products', 'counter', 'count', 'shop', 'archive' ];
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-table-of-contents';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'woocommerce-elements-single', 'woocommerce-elements-archive' ];
	}

	/**
	 * Render widget.
	 */
	protected function render() {
		if ( $this->is_edit_mode() ) {
			wc_setup_loop(
				[
					'total' => 2,
				]
			);
		}

		woocommerce_result_count();

		if ( $this->is_edit_mode() ) {
			wc_reset_loop();
		}
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'select_section',
			[
				'label' => esc_html__( 'Settings', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'text_align',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'              => 'left',
				'selectors_dictionary' => [
					'left'   => 'text-align: left;',
					'center' => 'text-align: center;',
					'right'  => 'text-align: right;',
				],
				'selectors'            => [
					'{{WRAPPER}} .woocommerce-result-count' => ' {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'label'    => esc_html__( 'Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .woocommerce-result-count',
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-result-count' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

}
