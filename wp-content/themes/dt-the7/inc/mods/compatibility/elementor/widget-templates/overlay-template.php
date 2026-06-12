<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widget_Templates;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Widget_Base;
use ElementorPro\Modules\QueryControl\Controls\Template_Query;
use ElementorPro\Modules\QueryControl\Module as QueryControlModule;
use The7\Mods\Compatibility\Elementor\Modules\Overlay\Module as OverlayModule;
use The7_Elementor_Compatibility;

defined( 'ABSPATH' ) || exit;

/**
 * Class Overlay_Template.
 *
 * @package The7\Mods\Compatibility\Elementor\Widget_Templates
 */
class Overlay_Template extends Abstract_Template {


	const TEMPLATE_CONTROL_KEY = 'the7_overlay_template';

	protected $post_thumbnail_id;

	/**
	 * Current widget stack.
	 *
	 * @var Widget_Base|null
	 */
	protected $current_widget = null;
	/**
	 * @return void
	 */
	public function add_controls() {
		if ( ! class_exists( OverlayModule::class, false )
			||
			! class_exists( Template_Query::class, false )
			||
			! class_exists( QueryControlModule::class, false )
		) {
			return;
		}

		$this->widget->start_controls_section(
			'overlay_template_section',
			[
				'label' => esc_html__( 'Overlay Template', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$hover_visibility_options = [
			'disabled'   => esc_html__( 'Disabled', 'the7mk2' ),
			'always'     => esc_html__( 'Always', 'the7mk2' ),
			'hover'      => esc_html__( 'Show on hover', 'the7mk2' ),
			'hover-hide' => esc_html__( 'Hide on hover', 'the7mk2' ),
		];
		$this->widget->add_responsive_control(
			'hover_visibility',
			[
				'label'                => esc_html__( 'Visibility', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'disabled',
				'options'              => $hover_visibility_options,
				'device_args'          => $this->widget->generate_device_args(
					[
						'options' => [ '' => esc_html__( 'No change', 'the7mk2' ) ] + $hover_visibility_options,
					]
				),
				'selectors_dictionary' => [
					'disabled'   => '--overlay-opacity: 0; --overlay-display: none; --overlay-hover-opacity: 0;',
					'always'     => '--overlay-opacity: 1; --overlay-display: flex; --overlay-hover-opacity: 1;',
					'hover'      => '--overlay-opacity: 0; --overlay-display: flex; --overlay-hover-opacity: 1;',
					'hover-hide' => '--overlay-opacity: 1; --overlay-display: flex; --overlay-hover-opacity: 0;',
				],
				'selectors'            => [
					'{{WRAPPER}}' => ' {{VALUE}};',
				],
				'prefix_class'         => 'the7-hover-visibility-',
				'frontend_available'   => true,
			]
		);

		$this->widget->add_control(
			self::TEMPLATE_CONTROL_KEY,
			[
				'label'              => esc_html__( 'Choose Overlay Template', 'the7mk2' ),
				'type'               => Template_Query::CONTROL_ID,
				'label_block'        => true,
				'autocomplete'       => [
					'object' => QueryControlModule::QUERY_OBJECT_LIBRARY_TEMPLATE,
					'query'  => [
						'post_status' => Document::STATUS_PUBLISH,
						'meta_query'  => [
							[
								'key'     => Document::TYPE_META_KEY,
								'value'   => OverlayModule::DOCUMENT_TYPE,
								'compare' => 'IN',
							],
						],
					],
				],
				'render_type'        => 'template',
				'actions'            => [
					'new'  => [
						'visible'         => true,
						'document_config' => [
							'type' => OverlayModule::DOCUMENT_TYPE,
						],
						'after_action'    => false,
					],
					'edit' => [
						'visible'      => true,
						'after_action' => false,
					],
				],
				'frontend_available' => true,
			]
		);

		$this->widget->end_controls_section();
	}

	/**
	 * @return mixed
	 */
	public function get_template_id() {
		return $this->get_settings( self::TEMPLATE_CONTROL_KEY );
	}

	/**
	 * @param int|null $image_id Post thumbnail ID.
	 *
	 * @return string
	 */
	public function get_render( $image_id = null ) {
		static $rendering = false;

		if ( $rendering ) {
			return '';
		}

		$rendering = true;

		/**
		 * Apply post thumbnail ID filter to get correct post thumbnail ID in template.
		 * Needed for cases when template is used outside loop widget, to fix "Featured Image Data".
		 */
		$this->apply_post_thumbnail_id_filter( $image_id );
		$template = The7_Elementor_Compatibility::get_builder_content_for_display( $this->get_template_id() );
		$this->revoke_post_thumbnail_id_filter();

		$rendering = false;

		return $template;
	}

	/**
	 * Return wrapper HTML class.
	 *
	 * @return string
	 */
	public function get_wrapper_class() {
		return 'the7-overlay-container';
	}

	/**
	 * @param int|null $post_thumbnail_id Post thumbnail ID.
	 *
	 * @return void
	 */
	protected function apply_post_thumbnail_id_filter( $post_thumbnail_id ) {
		if ( ! $post_thumbnail_id ) {
			return;
		}

		$this->post_thumbnail_id = $post_thumbnail_id;
		add_filter( 'post_thumbnail_id', [ $this, 'filter_post_thumbnail_id' ] );
		add_action( 'elementor/widget/before_render_content', [ $this, 'before_render_content' ] );
	}


	/**
	 * @return void
	 */
	protected function revoke_post_thumbnail_id_filter() {
		$this->post_thumbnail_id = null;
		$this->current_widget    = null;
		remove_filter( 'post_thumbnail_id', [ $this, 'filter_post_thumbnail_id' ] );
		remove_filter( 'elementor/widget/before_render_content', [ $this, 'before_render_content' ] );
	}


	/**
	 * Save latest widget instance
	 *
	 * @param Widget_Base $widget The widget.
	 */
	public function before_render_content( Widget_Base $widget ) {
		$this->current_widget = $widget;
	}


	/**
	 * @param int|false $post_thumbnail_id Post thumbnail ID.
	 *
	 * @return int|false
	 */
	public function filter_post_thumbnail_id( $post_thumbnail_id ) {
		// Fix initialization of nested elements located in the_content, force init frontend module.
		if (
			$this->current_widget &&
			$this->current_widget->get_name() === 'the7-woocommerce-loop-add-to-cart'
		) {
			return $post_thumbnail_id;
		}

		return $this->post_thumbnail_id ?: $post_thumbnail_id;
	}
}
