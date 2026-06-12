<?php

namespace The7\Mods\Compatibility\Elementor\Pro\Modules\Dynamic_Tags\The7\Tags;

use Elementor\Core\Base\Document;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Plugin as Elementor;
use ElementorPro\Modules\QueryControl\Module as QueryControlModule;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Dynamic_Tags\The7\Module;
use The7_Elementor_Compatibility;

defined( 'ABSPATH' ) || exit;

class The7_Template extends Tag {
	public function get_categories() {
		return [ Module::TEXT_CATEGORY_WITH_TEMPLATE ];
	}

	public function get_group() {
		return Module::THE7_GROUP;
	}

	public function get_title() {
		return __( 'The7 Template', 'the7mk2' );
	}

	public function get_name() {
		return 'the7-template';
	}

	public function render() {
		if ( ! did_action( 'wp_body_open' ) && ! Elementor::$instance->editor->is_edit_mode() ) {
			return;
		}

		$template_id = $this->get_settings( 'template_id' );

		if ( 'publish' !== get_post_status( $template_id ) ) {
			return;
		}

		?>
        <div class="elementor-template">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo The7_Elementor_Compatibility::get_builder_content_for_display( $template_id );
			?>
        </div>
		<?php
	}

	protected function register_controls() {
		$document_types = Elementor::$instance->documents->get_document_types( [
			'show_in_library' => true,
		] );

		$this->add_control( 'template_id', [
			'label'        => __( 'Choose Template', 'the7mk2' ),
			'type'         => QueryControlModule::QUERY_CONTROL_ID,
			'label_block'  => true,
			'autocomplete' => [
				'object' => QueryControlModule::QUERY_OBJECT_LIBRARY_TEMPLATE,
				'query'  => [
					'meta_query' => [
						[
							'key'     => Document::TYPE_META_KEY,
							'value'   => array_keys( $document_types ),
							'compare' => 'IN',
						],
					],
				],
			],
		] );
	}
}
