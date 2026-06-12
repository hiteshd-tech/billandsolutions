<?php
/**
 * Elementor loop template extension.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Modules\Loop;

use Elementor\Plugin;
use ElementorPro\Modules\LoopBuilder\Documents\Loop as Loop_Document;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Module_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Elementor tinymce extension module.
 */
class Module extends The7_Elementor_Module_Base {

	/**
	 * Recommended widget names.
	 *
	 * @var array
	 */
	const RECOMMENDED_WIDGET_NAMES = [
		'the7-heading',
		'the7-image-widget',
		'the7-taxonomies',
		'the7_button_widget',
	];

	/**
	 * Loop widgets.
	 *
	 * @var array
	 */
	const WIDGETS = [
		'slider-loop',
		'posts-loop',
        'loop-scroller',
	];

	/**
	 * Init module.
	 */
	public function __construct() {
		add_action( 'elementor/editor/init', [ __CLASS__, 'add_loop_templates' ] );
		add_filter( 'elementor/document/config', [ __CLASS__, 'add_document_config' ], 10, 2 );
	}

	/**
	 * @param array $additional_config  Additional config.
	 * @param int   $doc_id             Document ID.
	 *
	 * @return array
	 */
	public static function add_document_config( $additional_config, $doc_id ) {
		$document = Plugin::instance()->documents->get_doc_for_frontend( $doc_id );
		if ( $document && $document->get_template_type() === Loop_Document::DOCUMENT_TYPE ) {
			foreach ( static::RECOMMENDED_WIDGET_NAMES as $widget_to_recommend ) {
				$additional_config['panel']['widgets_settings'][ $widget_to_recommend ] = [
					'categories'    => [ 'recommended' ],
					'show_in_panel' => true,
				];
			}
		}

		return $additional_config;
	}

	/**
	 * @return void
	 */
	public static function add_loop_templates() {
		Plugin::instance()->common->add_template( __DIR__ . '/views/cta-template.php' );
	}

	/**
	 * Get module name.
	 * Retrieve the module name.
	 *
	 * @access public
	 * @return string Module name.
	 */
	public function get_name() {
		return 'loop';
	}

	/**
	 * @return bool
	 */
	public static function is_active() {
		return the7_elementor_pro_is_active() && (the7_is_elementor3_16() || Plugin::instance()->experiments->is_feature_active( 'loop' ));
	}

}
