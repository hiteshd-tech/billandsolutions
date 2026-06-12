<?php
/**
 * Handles registration and enqueuing of bundled assets.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Core;

defined( 'ABSPATH' ) || exit;

final class BundledAssetsManager {

	const BUNDLE_DIR = 'bundle/';

	const EDITOR_BUNDLE         = 'editor';
	const EDITOR_CONTENT_BUNDLE = 'editor-content';
	const VIEW_BUNDLE           = 'view';

	/**
	 * @var string Plugin ID used for building handles.
	 */
	private $plugin_id;

	/**
	 * @var string URL to the plugin's distribution directory.
	 */
	private $plugin_dist_url;

	/**
	 * @var string File system path to the plugin's distribution directory.
	 */
	private $plugin_dist;

	/**
	 * @var array Dependencies for each bundle (optional, can be empty). 
	 * Format: array( 'bundle_key' => array( 'dependency_handle1', 'dependency_handle2' ) )
	 */
	private $dependencies;

	public function __construct( string $plugin_id, string $plugin_dist, string $plugin_dist_url, array $dependencies = array() ) {
		$this->plugin_id       = $plugin_id;
		$this->plugin_dist     = $plugin_dist;
		$this->plugin_dist_url = $plugin_dist_url;
		$this->dependencies = $dependencies;
	}

	/**
	 * Register and enqueue editor assets for the block editor interface.
	 *
	 * @return void
	 */
	public function process_editor_assets(): void {
		$this->register_assets( self::EDITOR_BUNDLE );
		$this->enqueue_assets( self::EDITOR_BUNDLE );
	}

	/**
	 * Register and enqueue editor-content assets for block editor content area.
	 *
	 * @return void
	 */
	public function process_editor_content_assets(): void {
		$this->register_assets( self::EDITOR_CONTENT_BUNDLE );
		$this->enqueue_assets( self::EDITOR_CONTENT_BUNDLE );
	}

	/**
	 * Register and enqueue view assets for the frontend.
	 *
	 * @return void
	 */
	public function process_view_assets(): void {
		$this->register_assets( self::VIEW_BUNDLE );
		$this->enqueue_assets( self::VIEW_BUNDLE );
	}

	/**
	 * Add inline JS code just before bundle code (see wp_add_inline_script())
	 * Before mode does not affect "defer" script attribute
	 *
	 * @param string $bundle_name Bundle name(key) to add code to (see self::*_BUNDLE)
	 * @param string $js JS code to be added as inline script
	 *
	 * @return bool
	 */
	public function add_inline_js_before_bundle( $bundle_name, $js ): bool {
		return wp_add_inline_script(
			$this->build_script_handle( $bundle_name ),
			$js,
			'before'
		);
	}

	/**
	 * Add inline JS code to footer
	 * We use fake handler to non existing script to add inline code to footer
	 * Bundle name is required to add code with appropriate hook (editor, editor-content, view) 
	 * 
	 * @param string $bundle_name Bundle name (see self::*_BUNDLE) to add code with appropriate hook
	 * @param string $js JS code to be added as inline script
	 *
	 * @return bool
	 */
	public function add_inline_js_to_footer($bundle_name, $js ): bool {
		
		$handle = $this->build_script_handle( 'footer-inline' ) . '__handler';

		// register only once 
		if ( ! wp_script_is( $handle, 'registered' ) ) {
			wp_register_script(
				$handle,
				false, // no source file 
				array(), 
				false, 
				array( 'in_footer' => true ) // it won't be deferred because we add inline script to it
			);

			$action = $this->get_action_for_bundle( $bundle_name );
			
			if ( null === $action ) {
				return false;
			}

			add_action(
				$action, 
				function () use ( $handle ) {
					wp_enqueue_script( $handle );
				}
			);
		}

		return wp_add_inline_script( $handle, $js, 'before' );
	}

	/**
	 * Backward compatibility method to add inline JS code just after bundle code (see wp_add_inline_script())
	 * 
	 * @deprecated since version 1.4.0. Use add_inline_js_to_footer() instead.
	 * 
	 * @param string $bundle_name Bundle name (see self::*_BUNDLE) to add code with appropriate hook
	 * @param string $js JS code to be added as inline script
	 *
	 * @return bool
	 */
	public function add_inline_js_after_bundle( $bundle_name, $js ): bool {
		return $this->add_inline_js_to_footer( $bundle_name, $js );
	}

	/**
	 * Build a handle name for a given plugin ID, bundle key and type (script or style). 
	 *
	 * @param string $plugin_id   Plugin ID.
	 * @param string $bundle_key  Bundle key (one of 'editor', 'editor-content', 'view').
	 * @param string $type        Type of handle ('script' or 'style').
	 *
	 * @return string Handle name.
	 */
	public static function build_handle( $plugin_id, $bundle_key, $type ): string {
		return $plugin_id . '__bundle__' . $bundle_key . '-' . $type;
	}

	/**
	 * Get the appropriate action hook for enqueuing assets based on the bundle name.
	 * 
	 * @param string $bundle_name Bundle name(key) to get action for (see self::*_BUNDLE)
	 *
	 * @return string|null Action hook name or null if bundle name is invalid.
	 */
	private function get_action_for_bundle( $bundle_name ): ?string {
		$map = array(
			self::EDITOR_BUNDLE => 'enqueue_block_editor_assets',
			self::EDITOR_CONTENT_BUNDLE => 'enqueue_block_assets',
			self::VIEW_BUNDLE => 'wp_enqueue_scripts',
		);
		
		return array_key_exists( $bundle_name, $map ) ? $map[ $bundle_name ] : null;
	}

	/**
	 * Register script and style assets for a given bundle key.
	 *
	 * @param string $key Bundle key (one of 'editor', 'editor-content', 'view').
	 *
	 * @return void
	 */
	private function register_assets( $key ): void {
		if ( ! file_exists( $this->get_asset_filename( $key ) ) ) {
			return;
		}

		$asset_file = require $this->get_asset_filename( $key );

		// it's safe to return here as css is added only using js import construction
		if ( ! file_exists( $this->plugin_dist . self::BUNDLE_DIR . $key . '.js' ) ) {
			return;
		}

		// merge "native" dependencies from asset file with any additional dependencies provided added manually
		$script_dependencies = array_merge( $asset_file['dependencies'], (array)($this->dependencies[ $key ] ?? array()) );


		wp_register_script(
			$this->build_script_handle( $key ),
			$this->plugin_dist_url . self::BUNDLE_DIR . $key . '.js',
			$script_dependencies,
			$asset_file['version'],
			// it's important to use defer for all scripts in the bundle
			// otherwise the order of execution will be broken and it may cause errors
			// we add to header as it's common practice 
			array(
				'strategy'  => 'defer',
				'in_footer' => false,
			)
		);

		// style
		if ( ! file_exists( $this->plugin_dist . self::BUNDLE_DIR . $key . '.css' ) ) {
			return;
		}

		wp_register_style(
			$this->build_style_handle( $key ),
			$this->plugin_dist_url . self::BUNDLE_DIR . $key . '.css',
			array(), // no dependencies for styles
			$asset_file['version']
		);
	}

	/**
	 * Enqueue registered script and style assets for a given supported bundle key.
	 *
	 * @param string $key   Bundle key (one of 'editor', 'editor-content', 'view').
	 *
	 * @return void
	 */
	private function enqueue_assets( $key ): void {
		$script_handle = $this->build_script_handle( $key );
		$style_handle  = $this->build_style_handle( $key );

		$action = $this->get_action_for_bundle( $key );

		// if action is null it means that bundle key is invalid and we should not enqueue assets
		if ( null === $action ) {
			return;
		}

		add_action(
			$action,
			function () use ( $script_handle, $style_handle ) {
				wp_enqueue_script( $script_handle );
				wp_enqueue_style( $style_handle );
			}
		);
	}

	/**
	 * Get the asset metadata filename for a given bundle key.
	 *
	 * @param string $key Bundle key (one of 'editor', 'editor-content', 'view').
	 *
	 * @return string Path to the asset metadata file.
	 */
	private function get_asset_filename( $key ): string {
		return $this->plugin_dist . self::BUNDLE_DIR . $key . '.asset.php';
	}

	/**
	 * Build the script handle name for a given bundle key.
	 *
	 * @param string $key Bundle key (one of 'editor', 'editor-content', 'view').
	 *
	 * @return string Script handle name.
	 */
	public function build_script_handle( $key ): string {
		return self::build_handle( $this->plugin_id, $key, 'script' );
	}

	/**
	 * Build the style handle name for a given bundle key.
	 *
	 * @param string $key Bundle key (one of 'editor', 'editor-content', 'view').
	 *
	 * @return string Style handle name.
	 */
	private function build_style_handle( $key ): string {
		return self::build_handle( $this->plugin_id, $key, 'style' );
	}
}
