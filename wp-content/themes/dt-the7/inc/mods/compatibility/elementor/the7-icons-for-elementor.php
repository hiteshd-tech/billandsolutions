<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor;

defined( 'ABSPATH' ) || exit;

/**
 * Elemenor icons manager.
 */
class The7_Icons_For_Elementor {

	/**
	 * @return void
	 */
	public function use_the7_icons_in_elementor() {
		add_filter(
			'elementor/icons_manager/additional_tabs',
			function( $tabs ) {
				$tabs['the7-icons'] = [
					'name'          => 'the7-icons',
					'label'         => esc_html__( 'The7 Icons', 'the7mk2' ),
					'url'           => PRESSCORE_THEME_URI . '/fonts/icomoon-the7-font/icomoon-the7-font.min.css',
					'enqueue'       => [],
					'prefix'        => '',
					'displayPrefix' => '',
					'labelIcon'     => 'fab fa-font-awesome-alt',
					'ver'           => THE7_VERSION,
					'fetchJson'     => PRESSCORE_THEME_URI . '/fonts/icomoon-the7-font/icomoon-the7-font.js',
					'native'        => false,
				];

				return $tabs;
			},
			PHP_INT_MAX
		);
	}

	/**
	 * @return void
	 */
	public function use_elementor_icons_in_mega_menu() {
		add_action(
			'elementor/init',
			function() {
				add_filter( 'the7_icons_in_settings', [ $this, 'mega_menu_icons_list_filter' ] );
				add_action( 'optionsframework_load_styles', [ $this, 'enqueue_all_elementor_icons' ] );
				add_filter( 'presscore_nav_menu_item', [ $this, 'enqueue_elementor_icon_for_menu_item_filter' ], 10, 4 );
			}
		);
	}

	/**
	 * @param array $icons Icons definition.
	 *
	 * @return array
	 */
	public function mega_menu_icons_list_filter( $icons ) {
		$elementor_icons = \Elementor\Icons_Manager::get_icon_manager_tabs();
		$wp_content_url  = content_url();

		foreach ( $elementor_icons as $icon_type ) {
			if ( ! empty( $icon_type['fetchJson'] ) ) {
				$icons_file_path  = str_replace( $wp_content_url, WP_CONTENT_DIR, $icon_type['fetchJson'] );
				$icons_definition = wp_json_file_decode( $icons_file_path, [ 'associative' => true ] );
				if ( ! isset( $icons_definition['icons'] ) ) {
					continue;
				}

				$icon_type['icons'] = $icons_definition['icons'];
			}

			if ( empty( $icon_type['icons'] ) ) {
				continue;
			}

			$icon_label           = $icon_type['label'];
			$icons[ $icon_label ] = array_map(
				function( $icon ) use ( $icon_type ) {
					return $this->get_elementor_icon_prefix( $icon_type ) . $icon;
				},
				$icon_type['icons']
			);
		}

		return $icons;
	}

	/**
	 * @return void
	 */
	public function enqueue_all_elementor_icons() {
		// Register all Elementor icons.
		\Elementor\Plugin::$instance->icons_manager->register_styles();

		// Enqueue all Elementor icons.
		$config = \Elementor\Icons_Manager::get_icon_manager_tabs();
		foreach ( $config as $type => $icon_type ) {
			$this->enqueue_elementor_icon_font( $icon_type['name'] );
		}

		// Enqueue Elementor FontAwesome-Pro.
		if ( class_exists( '\ElementorPro\Modules\AssetsManager\Module' ) && method_exists( '\ElementorPro\Modules\AssetsManager\Module', 'get_assets_manager' ) ) {
			$icon_manager_object = \ElementorPro\Modules\AssetsManager\Module::instance()->get_assets_manager( 'icon' );
			if ( $icon_manager_object && method_exists( $icon_manager_object, 'get_icon_type_object' ) ) {
				$fa_pro = $icon_manager_object->get_icon_type_object( 'font-awesome-pro' );
				if ( $fa_pro && has_action( 'elementor/editor/after_enqueue_scripts', [ $fa_pro, 'enqueue_kit_js' ] ) ) {
					$fa_pro->enqueue_kit_js();
				}
			}
		}
	}

	/**
	 * @param string $menu_item Menu item HTML.
	 * @param string $title Menu item title.
	 * @param string $description Menu item description.
	 * @param object $item Menu item object.
	 *
	 * @return string
	 */
	public function enqueue_elementor_icon_for_menu_item_filter( $menu_item, $title, $description, $item ) {
		static $elementor_icons;

		if ( isset( $item->the7_mega_menu['menu-item-icon-type'], $item->the7_mega_menu['menu-item-icon'] ) && $item->the7_mega_menu['menu-item-icon-type'] === 'icon' && $item->the7_mega_menu['menu-item-icon'] ) {
			$menu_icon = $item->the7_mega_menu['menu-item-icon'];

			if ( ! isset( $elementor_icons ) ) {
				$elementor_icons = \Elementor\Icons_Manager::get_icon_manager_tabs();
			}

			foreach ( $elementor_icons as $type => $icon_type ) {
				$match = $this->get_elementor_icon_prefix( $icon_type );

				if ( ! $match || $this->is_elementor_icon_enqueued( $icon_type['name'] ) ) {
					unset( $elementor_icons[ $type ] );
					continue;
				}

				if ( strpos( $menu_icon, $match ) !== 0 ) {
					continue;
				}

				$this->enqueue_elementor_icon_font( $icon_type['name'] );
				unset( $elementor_icons[ $type ] );
				break;
			}
		}

		return $menu_item;
	}

	/**
	 * @param string $name Iconfont name.
	 *
	 * @return void
	 */
	protected function enqueue_elementor_icon_font( $name ) {
		wp_enqueue_style( 'elementor-icons-' . $name );
	}

	/**
	 * @param sring $name Iconfont name.
	 *
	 * @return bool
	 */
	protected function is_elementor_icon_enqueued( $name ) {
		return wp_style_is( 'elementor-icons-' . $name, 'enqueued' );
	}

	/**
	 * @param array $icon Iconfont definition.
	 *
	 * @return string
	 */
	protected function get_elementor_icon_prefix( $icon ) {
		if ( empty( $icon['displayPrefix'] ) && empty( $icon['prefix'] ) ) {
			return '';
		}

		return trim( $icon['displayPrefix'] . ' ' . $icon['prefix'] );
	}
}
