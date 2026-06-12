<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Modules\Loop;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Plugin as Elementor;
use Elementor\Repeater;
use ElementorPro\Modules\LoopBuilder\Documents\Loop as LoopDocument;
use ElementorPro\Modules\QueryControl\Controls\Template_Query;
use ElementorPro\Modules\QueryControl\Module as QueryControlModule;

/**
 * Trait to handle alternate templates in the loop.
 */
trait Alternate_Template_Trait {

    protected $posts_per_page = null;
    protected $paged = 1;
	protected $query = null;

	/**
	 * @var int
	 */
	private $current_post_index = 0;

	/**
	 * @var array
	 */
	private $alternate_templates = [];

	/**
	 * @var array
	 */
	private $rendered_alternate_templates = [];

	/**
	 * @var bool
	 */
	private $has_alternate_templates = false;

	/**
	 * @var bool
	 */
	private $has_static_alternate_templates = false;

	/**
	 * @var bool
	 */
	private $query_contains_static_alternate_templates = false;

	/**
	 * @var array
	 */
	private $static_alternate_template_query_data = [];

	/**
	 * @return void
	 */
	protected function add_alternate_templates_controls() {
		$this->add_control(
			'alternate_template',
			[
				'label'              => esc_html__( 'Apply an alternate template', 'the7mk2' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_off'          => esc_html__( 'Off', 'the7mk2' ),
				'label_on'           => esc_html__( 'On', 'the7mk2' ),
				'condition'          => [
					'posts_per_page!' => 1,
					'template_id!'    => '',
				],
				'render_type'        => 'template',
				'frontend_available' => true,
				'separator'          => 'before',
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'template_id',
			[
				'label'              => esc_html__( 'Choose Loop Template', 'the7mk2' ),
				'type'               => Template_Query::CONTROL_ID,
				'label_block'        => true,
				'autocomplete'       => [
					'object' => QueryControlModule::QUERY_OBJECT_LIBRARY_TEMPLATE,
					'query'  => [
						'post_status' => Document::STATUS_PUBLISH,
						'meta_query'  => [
							[
								'key'     => Document::TYPE_META_KEY,
								'value'   => LoopDocument::get_type(),
								'compare' => 'IN',
							],
						],
					],
				],
				'actions'            => [
					'new'  => [
						'visible'         => true,
						'document_config' => [
							'type' => LoopDocument::get_type(),
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

		$repeater->add_control(
			'repeat_template',
			[
				'label'     => esc_html__( 'Position in grid', 'the7mk2' ),
				'type'      => Controls_Manager::NUMBER,
				'condition' => [
					'template_id!' => '',
				],
			]
		);

		$repeater->add_control(
			'repeat_template_note',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Note: Repeat the alternate template once every chosen number of items.', 'the7mk2' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => [
					'template_id!' => '',
				],
			]
		);

		$repeater->add_control(
			'show_once',
			[
				'label'       => esc_html__( 'Apply Once', 'the7mk2' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'label_off'   => esc_html__( 'No', 'the7mk2' ),
				'label_on'    => esc_html__( 'Yes', 'the7mk2' ),
				'condition'   => [
					'template_id!' => '',
				],
				'render_type' => 'template',
			]
		);

		$repeater->add_responsive_control(
			'column_span',
			[
				'label'     => esc_html__( 'Column Span', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '1',
				'options'   => [
					'1'  => '1',
					'2'  => '2',
					'3'  => '3',
					'4'  => '4',
					'5'  => '5',
					'6'  => '6',
					'7'  => '7',
					'8'  => '8',
					'9'  => '9',
					'10' => '10',
					'11' => '11',
					'12' => '12',
				],
				'condition' => [
					'template_id!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'grid-column: span min( {{VALUE}}, var(--grid-columns) );',
				],
			]
		);

		$repeater->add_control(
			'column_span_note',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Note: Item will span across a number of columns.', 'the7mk2' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => [
					'template_id!' => '',
				],
			]
		);

		$repeater->add_control(
			'column_span_masonry_note',
			[
				// TODO: Remove define() with the release of Elementor 3.22
				'type'       => defined( 'Controls_Manager::ALERT' ) ? Controls_Manager::ALERT : 'alert',
				'alert_type' => 'warning',
				'content'    => esc_html__( 'Note: The Masonry option combined with Column Span might cause unexpected results and break the layout.', 'the7mk2' ),
				'condition'  => [
					'column_span!' => '1',
				],
			]
		);

		$repeater->add_control(
			'static_position',
			[
				'label'       => esc_html__( 'Static item position', 'the7mk2' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_off'   => esc_html__( 'Off', 'the7mk2' ),
				'label_on'    => esc_html__( 'On', 'the7mk2' ),
				'condition'   => [
					'template_id!' => '',
				],
				'render_type' => 'template',
			]
		);

		$repeater->add_control(
			'static_position_note',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Note: Can be used for featured posts, promo banners etc. Static Items remain in place when new items are added to grid. Other items appear according to query settings.', 'the7mk2' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => [
					'static_position!' => '',
					'template_id!'     => '',
				],
			]
		);

		$this->add_control(
			'alternate_templates',
			[
				'label'       => esc_html__( 'Templates', 'the7mk2' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => 'Alternate Template',
				'condition'   => [
					'alternate_template' => 'yes',
					'posts_per_page!'    => 1,
					'template_id!'       => '',
				],
				'default'     => [
					[
						'template_id' => null,
					],
				],
			]
		);
	}

	/**
	 * @return bool
	 */
	private function has_alternate_templates() {
		return $this->has_alternate_templates;
	}

	/**
	 * @param array $attributes Elementor wrapper attributes.
	 *
	 * @return array
	 */
	public function add_alternate_template_wrapper_classes( $attributes ) {
		$template = $this->get_template_data_for_current_post();

		if ( $this->is_alternate_template( $template ) ) {
			$attributes['class'] .= ' elementor-repeater-item-' . $template['_id'];
		}

		return $attributes;
	}

	/**
	 * @param array $alternate_template Alternate template settings.
	 *
	 * @return bool
	 */
	private function is_alternate_template( $alternate_template ) {
		return isset( $alternate_template['alternate_template'] ) && 'yes' === $alternate_template['alternate_template'];
	}

	/**
	 * @param array $attributes Elementor wrapper attributes.
	 *
	 * @return array
	 */
	public function add_alternate_template_editor_wrapper_classes( $attributes ) {
		$template = $this->get_template_data_for_current_post();

		if ( $this->is_alternate_template( $template ) && $this->is_alternate_template_first_occurrence( $template ) ) {
			$attributes['class'] .= ' e-loop-alternate-template';
		}

		return $attributes;
	}

	/**
	 * @param array $template Alternate template settings.
	 *
	 * @return bool
	 */
	private function is_alternate_template_first_occurrence( $template ) {
		return ! in_array( $template['template_id'], $this->rendered_alternate_templates, true );
	}

	/**
	 * @return void
	 */
	private function alternate_template_before_render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['alternate_templates'] ) || empty( $settings['template_id'] ) ) {
			return;
		}

		$this->has_alternate_templates = true;

		$alternate_templates = $settings['alternate_templates'];

		$alternate_templates = array_reverse( $alternate_templates );

		// Rearrange the alternate template settings to group all static templates before the standard templates.
		$static_alternate_templates   = [];
		$standard_alternate_templates = [];
		foreach ( $alternate_templates as $alternate_template ) {
			// Skip the alternate template from any calculations until a repeat template number is specified.
			if ( empty( $alternate_template['repeat_template'] ) ) {
				continue;
			}

			if ( $this->is_alternate_template_static_position( $alternate_template ) ) {
				$static_alternate_templates[ $alternate_template['_id'] ] = $alternate_template;

				// Note that the user has added 'static' alternate templates.
				$this->has_static_alternate_templates = true;
			} else {
				$standard_alternate_templates[ $alternate_template['_id'] ] = $alternate_template;
			}
		}

		$this->alternate_templates = $static_alternate_templates + $standard_alternate_templates;

		if ( ! $this->has_alternate_templates() ) {
			return;
		}

		$this->maybe_add_alternate_template_wrapper_classes();
	}

	/**
	 * @return void
	 */
	private function maybe_add_alternate_template_wrapper_classes() {
		add_filter( 'elementor/document/wrapper_attributes', [ $this, 'add_alternate_template_wrapper_classes' ] );

		if ( Elementor::$instance->editor->is_edit_mode() ) {
			add_filter( 'elementor/document/wrapper_attributes', [ $this, 'add_alternate_template_editor_wrapper_classes' ] );
		}
	}

	/**
	 * @param array $alternate_template Alternate template settings.
	 *
	 * @return bool
	 */
	private function is_alternate_template_static_position( $alternate_template ) {
		return isset( $alternate_template['static_position'] ) && 'yes' === $alternate_template['static_position'];
	}

	/**
	 * @return int|mixed
	 */
	private function get_current_post_index() {
		if ( $this->query_contains_static_alternate_templates() ) {
			return $this->get_static_alternate_template_start_index() + $this->current_post_index;
		}

		return isset( $this->query, $this->query->current_post ) ? $this->query->current_post : 0;
	}

	/**
	 * @return false|mixed
	 */
	private function get_static_alternate_template_start_index() {
		$current_page_settings = $this->get_static_alternate_template_current_page_settings();
		if ( ! $current_page_settings ) {
			return false;
		}
		return $current_page_settings['start_index'];
	}

	/**
	 * @param int $index Index of the post in the loop.
	 *
	 * @return array|mixed
	 */
	private function get_data_for_static_alternate_template( $index ) {
		if ( ! empty( $this->static_alternate_template_query_data['templates'][ $index ] ) ) {
			return $this->static_alternate_template_query_data['templates'][ $index ];
		}

		return $this->get_default_template();
	}

	/**
	 * @return array
	 */
	private function get_default_template() {
		return [
			'template_id'        => $this->get_settings_for_display( 'template_id' ),
			'alternate_template' => 'no',
			'static_position'    => 'no',
			'_id'                => '-',
		];
	}

	/**
	 * @return bool
	 */
	private function query_contains_static_alternate_templates() {
		return $this->query_contains_static_alternate_templates;
	}

	/**
	 * @return false|mixed
	 */
	private function get_static_alternate_template_current_page_settings() {
		$current_page = $this->paged;
		if ( empty( $this->static_alternate_template_query_data['page_settings'][ $current_page ] ) ) {
			return false;
		}

		return $this->static_alternate_template_query_data['page_settings'][ $current_page ];
	}

	/**
	 * @return false|mixed
	 */
	private function get_static_alternate_template_query_offset() {
		$current_page_settings = $this->get_static_alternate_template_current_page_settings();
		if ( ! $current_page_settings ) {
			return false;
		}

		return $current_page_settings['query_offset'];
	}

	/**
	 * @return false|\WP_Query
	 */
	public function query_posts_for_alternate_templates() {
		// If there are no static alternate templates, no need to modify the query.
		if ( ! $this->has_static_alternate_templates() ) {
			return false;
		}

		/**
		 * Construct the `static_alternate_template_query_data` used for the new query and when rendering each post.
		 */
		$query = $this->make_query(
			[
				'posts_per_page' => 1,
				'paged'          => 1,
			]
		);
		$this->init_static_alternate_template_query_data( $query->found_posts );

		if ( ! $this->query_contains_static_alternate_templates() ) {
			return false;
		}

		$adjusted_found_posts   = $this->get_static_alternate_template_adjusted_found_posts();
		$adjusted_max_num_pages = $this->get_static_alternate_template_adjusted_max_num_pages();

		/**
		 * New query using `offset` in place of `paged`.
		 */

		add_action( 'pre_get_posts', [ $this, 'add_offset' ] );

		$query = $this->make_query(
			[
				'offset_fix' => $this->get_static_alternate_template_query_offset(),
			]
		);

		remove_action( 'pre_get_posts', [ $this, 'add_offset' ] );

		/**
		 * Increase size of the query using the adjusted values calculated after
		 * constructing `static_alternate_template_query_data`.
		 */
		$query->found_posts   = $adjusted_found_posts;
		$query->max_num_pages = $adjusted_max_num_pages;

		return $query;
	}

    public function make_query($query_args = []) {
        return $this->query($query_args);
    }

	/**
	 * @param \WP_Query $query WP_Query instance.
	 *
	 * @return void
	 */
	public function add_offset( $query ) {
		$offset = (int) $query->get( 'offset_fix' );
		$query->set( 'offset', $offset );
	}

	/**
	 * @return int|null
	 */
	private function get_static_alternate_template_adjusted_found_posts() {
		if ( empty( $this->static_alternate_template_query_data['templates'] ) ) {
			return 0;
		}
		return count( $this->static_alternate_template_query_data['templates'] );
	}

	/**
	 * @return false|float
	 */
	private function get_static_alternate_template_adjusted_max_num_pages() {
		return ceil( $this->get_static_alternate_template_adjusted_found_posts() / $this->posts_per_page );
	}

	/**
	 * @param int $required_posts_count Number of posts required.
	 *
	 * @return void
	 */
	private function init_static_alternate_template_query_data( $required_posts_count ) {
		$this->static_alternate_template_query_data = [
			'templates'     => [],
			'page_settings' => [],
		];

		$posts_per_page                  = $this->posts_per_page;
		$static_alternate_template_count = 0;

		for ( $current_index = 0; $current_index < $required_posts_count; $current_index++ ) {
			$template = $this->get_template_data_by_index( $current_index );
			$this->set_static_alternate_template_query_data_item( $template, $current_index, $static_alternate_template_count, $posts_per_page );

			if ( 'yes' === $template['static_position'] ) {
				++$static_alternate_template_count;
				++$required_posts_count;
			}
		}

		// Note if any valid 'static' alternate templates need to be displayed, after the above calculations.
		$this->query_contains_static_alternate_templates = 0 < $static_alternate_template_count;
	}

	/**
	 * @param array $template                        Alternate template settings.
	 * @param int   $current_post_index              Current post index.
	 * @param int   $static_alternate_template_count Static alternate template count.
	 * @param int   $posts_per_page                  Posts per page.
	 *
	 * @return void
	 */
	private function set_static_alternate_template_query_data_item( $template, $current_post_index, $static_alternate_template_count, $posts_per_page ) {
		// Store template - used when we render the post.
		$this->static_alternate_template_query_data['templates'][ $current_post_index ] = $template;

		// Store `page_settings`.
		// `query_offset` is used when we query posts and `start_index` when we render posts.
		$current_page = ceil( $current_post_index / $posts_per_page ) + 1;

		$post_offset = (int) $this->get_settings_for_display( 'posts_offset' );

		$this->static_alternate_template_query_data['page_settings'][ $current_page ] = [
			'query_offset' => $current_post_index - $static_alternate_template_count + $post_offset,
			'start_index'  => $current_post_index,
		];
	}

	/**
	 * @return array|mixed
	 */
	private function get_template_data_for_current_post() {
		$current_post_index = $this->get_current_post_index();

		if ( $this->query_contains_static_alternate_templates() ) {
			return $this->get_data_for_static_alternate_template( $current_post_index );
		}

		return $this->get_template_data_by_index( $current_post_index );
	}

	/**
	 * @return void
	 */
	private function render_post_alternate_templates() {
		// If any static templates are rendered they will result in this function being called recursively, so we need
		// to make sure we don't render more posts than the user has chosen in their `posts_per_page` widget control.
		if ( $this->posts_per_page && $this->current_post_index >= $this->posts_per_page ) {
			return;
		}

		$template = $this->get_template_data_for_current_post();
		$this->render_post_content( $template['template_id'] );
		$this->store_rendered_alternate_templates( $template['template_id'] );
		++$this->current_post_index;

		// If the current post has a 'static' alternate template the above will render an extra empty post in the loop.
		// We need to render this post again (with incremented `current_post_index`) so it is not skipped as a result
		// of a static template.
		if ( $this->is_alternate_template_static_position( $template ) ) {
			$this->render_post_alternate_templates();
		}
	}

	/**
	 * @param array $alternate_template Alternate template settings.
	 *
	 * @return bool
	 */
	private function is_alternate_template_show_once( $alternate_template ) {
		return isset( $alternate_template['show_once'] ) && 'yes' === $alternate_template['show_once'];
	}

	/**
	 * @param array $alternate_template Alternate template settings.
	 * @param int   $current_item_index Current item index.
	 *
	 * @return bool
	 */
	private function should_show_alternate_template_once( $alternate_template, $current_item_index ) {
		return $alternate_template['repeat_template'] === $current_item_index + 1;
	}

	/**
	 * @param int $number_to_check   Number to check.
	 * @param int $multiple_to_check Multiple to check.
	 *
	 * @return bool
	 */
	private function is_repeating_alternate_template_multiple_of( $number_to_check, $multiple_to_check ) {
		return 0 === $multiple_to_check % $number_to_check;
	}

	/**
	 * @param array $alternate_template Alternate template settings.
	 * @param int   $current_item_index Current item index.
	 *
	 * @return bool
	 */
	private function should_show_repeating_alternate_template( $alternate_template, $current_item_index ) {
		return $this->is_repeating_alternate_template_multiple_of( $alternate_template['repeat_template'], $current_item_index + 1 );
	}

	/**
	 * @param int $index Index of the post in the loop.
	 *
	 * @return array
	 */
	private function get_template_data_by_index( $index ) {
		if ( ! $this->has_alternate_templates() ) {
			return $this->get_default_template();
		}

		foreach ( $this->alternate_templates as $alternate_template ) {
			$found_alternate_template = $this->is_alternate_template_show_once( $alternate_template ) ?
				$this->should_show_alternate_template_once( $alternate_template, $index ) :
				$this->should_show_repeating_alternate_template( $alternate_template, $index );

			if ( $found_alternate_template ) {
				if ( $alternate_template['repeat_template'] === 1 && !$this->is_alternate_template_show_once( $alternate_template ) && $this->is_alternate_template_static_position( $alternate_template ) ) {
					break; // return default template to prevent infinity loop
				}
				return [
					'template_id'        => $alternate_template['template_id'],
					'alternate_template' => 'yes',
					'static_position'    => $alternate_template['static_position'] ? 'yes' : 'no',
					'_id'                => $alternate_template['_id'],
				];
			}
		}

		return $this->get_default_template();
	}

	/**
	 * @param string $template_id Template ID.
	 *
	 * @return void
	 */
	private function store_rendered_alternate_templates( $template_id ) {
		if ( ! in_array( $template_id, $this->rendered_alternate_templates, true ) ) {
			$this->rendered_alternate_templates[] = $template_id;
		}
	}

	/**
	 * @return bool
	 */
	private function has_static_alternate_templates() {
		return $this->has_static_alternate_templates;
	}

	/**
	 * @return void
	 */
	private function alternate_template_after_render() {
		$this->current_post_index                        = 0;
		$this->alternate_templates                       = [];
		$this->rendered_alternate_templates              = [];
		$this->has_alternate_templates                   = false;
		$this->has_static_alternate_templates            = false;
		$this->query_contains_static_alternate_templates = false;
		$this->static_alternate_template_query_data      = [];
	}
}
