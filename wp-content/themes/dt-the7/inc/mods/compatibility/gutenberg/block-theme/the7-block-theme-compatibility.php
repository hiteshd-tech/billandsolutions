<?php
/**
 * The7 block theme compatibility class.
 *
 * @since   12.0.0
 * @package The7
 */

namespace The7\Mods\Compatibility\Gutenberg\Block_Theme;

defined( 'ABSPATH' ) || exit;

/**
 * Class The7_Block_Theme_Compatibility
 */
class The7_Block_Theme_Compatibility {

	const FSE_VERSION_OPTION = 'the7_fse_version';

	/**
	 * Instance.
	 *
	 * @access public
	 * @static
	 * @var The7_Block_Theme_Compatibility
	 */
	public static $instance = null;

	/**
	 * Get FSE files source directory.
	 *
	 * @return string
	 */
	protected function get_fse_files_src_dir(): string {
		$src_dir = get_template_directory();
		$version = $this->get_fse_version();

		/**
		 * Filters The7 FSE files source directory, where the FSE files are loaded from.
		 *
		 * @param string $src_dir The source directory path.
		 * @return string The filtered source directory path.
		 */
		return (string) apply_filters( 'the7_fse_files_src_dir', "{$src_dir}/fse/versions/v{$version}" );
	}

	/**
	 * Get FSE files destination directory.
	 *
	 * @return string
	 */
	protected function get_fse_files_dest_dir(): string {
		/**
		 * Filters The7 FSE files destination directory, where the FSE files are copied to.
		 *
		 * @param string $dest_dir The destination directory path.
		 * @return string The filtered destination directory path.
		 */
		return (string) apply_filters( 'the7_fse_files_dest_dir', get_template_directory() );
	}

	/**
	 * Instance.
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @return The7_Block_Theme_Compatibility An instance of the class.
	 * @access public
	 * @static
	 */
	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
			self::$instance->bootstrap();
		}

		return self::$instance;
	}

	/**
	 * Get FSE files list to copy.
	 *
	 * @return array
	 */
	public static function get_fse_files_list() {
		return [
			'theme.json',
			'templates',
			'parts',
			'patterns',
			'styles',
		];
	}

	/**
	 * Bootstrap module.
	 */
	public function bootstrap() {
		// Transform into block theme early to allow other code to work with it.
		$this->maybe_transform_the7_in_to_block_theme();

		if ( the7_is_gutenberg_theme_mode_active() ) {
			add_action( 'init', [ $this, 'load_blocks_customization' ] );

			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
			add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_styles' ] );

			The7_FSE_Font_Manager::instance()->init();

			// WP SEO compatibility.
			if ( defined( 'WPSEO_VERSION' ) ) {
				add_action(
					'wp_head',
					function () {
						remove_action( 'wp_head', '_block_template_render_title_tag', 1 );
					},
					0
				);
			}

			// Ignore the7 templates in WooCommerce.
			add_filter( 'wc_get_template', [ $this, 'filter_woocommerce_templates' ], 50, 5 );
			add_filter( 'wc_get_template_part', [ $this, 'filter_woocommerce_template_part' ], 50, 3 );
		} else {
			remove_theme_support( 'block-templates' );
		}

		// Reset fonts upon turning FSE mode on, to trigger font download immediately in admin.
		add_action(
			'the7_dashboard_before_settings_save',
			static function ( $settings, $old_settings ) {
				if (
					isset( $settings['settings-preset'] )
					&& $settings['settings-preset'] === 'gutenberg'
					&& (
						empty( $old_settings['settings-preset'] )
						|| $old_settings['settings-preset'] !== 'gutenberg'
					)
				) {
					The7_FSE_Font_Manager::instance()->reset_fonts_to_download();
				}
			},
			10,
			2
		);
	}

	/**
	 * @return void
	 */
	public function enqueue_styles() {
		$fse_version = absint( $this->get_fse_version() );

		the7_register_style(
			'the7-fse-styles',
			PRESSCORE_THEME_URI . '/fse/versions/v' . $fse_version . '/global.css'
		);
		wp_enqueue_style( 'the7-fse-styles' );
	}

	/**
	 * @return void
	 */
	public function load_blocks_customization() {
		$fse_version = absint( $this->get_fse_version() );

		$blocks_customization = PRESSCORE_THEME_DIR . '/fse/versions/v' . $fse_version . '/blocks-customization.php';

		if ( file_exists( $blocks_customization ) ) {
			require_once $blocks_customization;
		}
	}

	/**
	 * @return void
	 */
	public function maybe_transform_the7_in_to_block_theme() {
		do_action( 'the7_before_maybe_transform_in_to_block_theme' );

		if ( the7_is_gutenberg_theme_mode_active() && ! wp_is_block_theme() ) {
			$this->copy_block_theme_files();
		} elseif ( ! the7_is_gutenberg_theme_mode_active() && wp_is_block_theme() ) {
			$this->delete_block_theme_files();
		}
	}

	/**
	 * @return bool
	 */
	public function copy_block_theme_files() {
		$this->delete_block_theme_files();

		$from       = $this->get_fse_files_src_dir();
		$to         = $this->get_fse_files_dest_dir();
		$filesystem = the7_get_filesystem();

		if ( ! $from || ! $to || is_wp_error( $filesystem ) ) {
			return false;
		}

		$dirlist = $filesystem->dirlist( $from );
		if ( ! $dirlist ) {
			return false;
		}

		$exclude_files = array_diff(
			array_keys( $dirlist ),
			self::get_fse_files_list()
		);

		copy_dir( $from, $to, array_values( $exclude_files ) );

		wp_get_theme()->cache_delete();

		return true;
	}

	/**
	 * Get FSE version. Note. On theme update we should save the version of the current FSE files. On demo import it is enought to delete
	 *
	 * @return string
	 */
	public function get_fse_version() {
		$version = get_option( self::FSE_VERSION_OPTION );

		return $this->is_valid_version( $version ) ? $version : PRESSCORE_FSE_VERSION;
	}

	/**
	 * Check if FSE version exists.
	 *
	 * @return bool
	 */
	public function fse_version_exists() {
		return get_option( self::FSE_VERSION_OPTION ) !== false;
	}

	/**
	 * Set FSE version and copy files.
	 *
	 * @param string $version only digits and dot as a separator.
	 *
	 * @return bool
	 */
	public function set_fse_version( $version ) {
		if ( $this->is_valid_version( $version ) ) {
			update_option( self::FSE_VERSION_OPTION, $version, true );
			$this->copy_block_theme_files();
			return true;
		}
		return false;
	}

	/**
	 * Check if version is valid.
	 *
	 * @param string $version Version string.
	 *
	 * @return bool
	 */
	private function is_valid_version( $version ) {
		return preg_match( '/^\d+(\.\d+)*$/', $version );
	}

	/**
	 * Delete FSE version.
	 *
	 * @return bool
	 */
	public function delete_fse_version() {
		return delete_option( self::FSE_VERSION_OPTION );
	}

	/**
	 * @return bool
	 */
	public function delete_block_theme_files() {
		$dir        = $this->get_fse_files_dest_dir();
		$filesystem = the7_get_filesystem();
		if ( ! $dir || is_wp_error( $filesystem ) ) {
			return false;
		}

		$files_to_remove = self::get_fse_files_list();
		foreach ( $files_to_remove as $file ) {
			$file_path = $dir . '/' . $file;

			if ( $filesystem->exists( $file_path ) ) {
				$filesystem->delete( $file_path, true );
			}
		}
		wp_get_theme()->cache_delete();

		return true;
	}

	/**
	 * Fix WooCommerce templates loader.
	 *
	 * @param string $template      Template.
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments.
	 * @param string $template_path Path.
	 * @param string $default_path  Default path.
	 *
	 * @return string
	 */
	public function filter_woocommerce_templates( $template, $template_name, $args, $template_path, $default_path ) {
		if ( $this->is_presscore_template( $template ) || ( defined( 'WC_TEMPLATE_DEBUG_MODE' ) && WC_TEMPLATE_DEBUG_MODE ) ) {
			// Get default template/.
			$default_path = WC()->plugin_path() . '/templates/';
			if ( version_compare( WC()->version, '3.7.0', '>=' ) ) {
				if ( false !== strpos( $template_name, 'product_cat' ) || false !== strpos( $template_name, 'product_tag' ) ) {
					$cs_template = str_replace( '_', '-', $template_name );
				}
			}
			// Get default template/.
			if ( empty( $cs_template ) ) {
				$template = $default_path . $template_name;
			} else {
				$template = $default_path . $cs_template;
			}
		}

		return $template;
	}

	/**
	 * Fix WooCommerce template parts loader.
	 *
	 * @param string $template Template.
	 * @param string $slug     Slug.
	 * @param string $name     Name.
	 *
	 * @return string
	 */
	public function filter_woocommerce_template_part( $template, $slug, $name ) {
		if ( $this->is_presscore_template( $template ) ) {
			$fallback = WC()->plugin_path() . "/templates/{$slug}-{$name}.php";
			$template = file_exists( $fallback ) ? $fallback : '';
		}

		return $template;
	}

	protected function is_presscore_template( $template ) {
		return strpos( trailingslashit( $template ), trailingslashit( PRESSCORE_THEME_DIR ) ) !== false;
	}
}
