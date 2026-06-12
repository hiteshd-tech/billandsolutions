<?php

namespace The7\Mods\Compatibility\Elementor\Pro\Modules\Dynamic_Tags\The7;

use Elementor\Element_Base;
use Elementor\Modules\DynamicTags\Module as TagsModule;

defined( 'ABSPATH' ) || exit;

class Module extends TagsModule {

	const THE7_GROUP                  = 'the7';
	const TEXT_CATEGORY_WITH_TEMPLATE = 'the7-text-with-template';

	/**
	 * @var array
	 */
	private $tag_class_names = [];

	/**
	 * Dynamic tags module constructor.
	 *
	 * Initializing Elementor dynamic tags module.
	 */
	public function __construct() {
		parent::__construct();
		if ( the7_elementor_pro_is_active() ) {
			$this->include_tags( 'the7-color' );
			$this->include_tags( 'the7-template' );
			$this->include_tags( 'the7-brand-name' );
			$this->include_tags( 'the7-brand-image' );
			$this->include_tags( 'the7-brand-link' );
		}
	}

	/**
	 * @param string $tag_filename Tag file name.
	 *
	 * @return void
	 */
	private function include_tags( $tag_filename ) {
		require_once __DIR__ . '/tags/' . $tag_filename . '.php';

		$class_name = str_replace( '-', '_', $tag_filename );

		$this->tag_class_names[] = ucwords( $class_name, '_' );
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return 'the7-tags';
	}

	/**
	 * @return array
	 */
	public function get_tag_classes_names() {
		return $this->tag_class_names;
	}

	/**
	 * @return array[]
	 */
	public function get_groups() {
		return [
			self::THE7_GROUP => [
				'title' => __( 'The7', 'the7mk2' ),
			],
		];
	}
}
