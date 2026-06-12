<?php
/**
 * Demo representation.
 *
 * Provides helpers for demo metadata used by the Demo Content importer.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent\Demo;

defined( 'ABSPATH' ) || exit;

/**
 * Class Demo
 *
 * Represents a demo package and its import state.
 */
class Demo {

	const DEMO_STATUS_FULL_IMPORT  = 'full_import';
	const DEMO_STATUS_NOT_IMPORTED = 'not_imported';

	/**
	 * Whether demo post types have been imported.
	 *
	 * @var bool
	 */
	public $post_types_imported;

	/**
	 * Whether demo attachments have been imported.
	 *
	 * @var bool
	 */
	public $attachments_imported;

	/**
	 * Current import status identifier.
	 *
	 * @var string
	 */
	protected $import_status;

	/**
	 * Raw demo fields keyed by property name.
	 *
	 * @var array
	 */
	protected $fields = array();

	/**
	 * Demo constructor.
	 *
	 * @param array $demo    Demo data.
	 */
	public function __construct( $demo ) {
		$this->setup_fields( $demo );
		$this->refresh_import_status();
	}

	/**
	 * Refreshes import status flags based on history data.
	 *
	 * @return void
	 */
	public function refresh_import_status() {
		$this->post_types_imported  = false;
		$this->attachments_imported = false;

		$this->import_status = $this->get_import_status();
	}

	/**
	 * Gets a formatted import status label.
	 *
	 * @return string HTML markup containing the status label.
	 */
	public function get_import_status_text() {
		$text = '';
		if ( $this->import_status === static::DEMO_STATUS_FULL_IMPORT ) {
			$text = '(' . esc_html__( 'fully imported', 'better-block-editor' ) . ')';
		}

		return '<span class="demo-import-status">' . $text . '</span>';
	}

	/**
	 * Determines whether importing the demo is allowed.
	 *
	 * @return bool Whether import can proceed.
	 */
	public function import_allowed() {
		return true;
	}

	/**
	 * Gets the required plugin list.
	 *
	 * @return array List of plugins associated with the demo.
	 */
	public function plugins() {
		return (array) $this->fields['required_plugins'];
	}

	/**
	 * Gets the temporary uploads directory for the demo package.
	 *
	 * @return string Absolute path to the temporary uploads directory.
	 */
	public function get_demo_uploads_dir() {
		$wp_uploads = wp_get_upload_dir();

		return trailingslashit( $wp_uploads['basedir'] ) . "wpbbe-demo-content-tmp/{$this->id}";
	}

	/**
	 * Gets the XML file path for a full content import.
	 *
	 * @return string Absolute path to the XML file.
	 */
	public function get_import_xml_file() {
		return $this->get_demo_uploads_dir() . '/full-content.xml';
	}

	/**
	 * Gets the meta information JSON file path.
	 *
	 * @return string Absolute path to the JSON file.
	 */
	public function get_import_meta_file() {
		return $this->get_demo_uploads_dir() . '/site-meta.json';
	}

	/**
	 * Retrieves import meta data for the demo.
	 *
	 * @param string|null $key Optional meta key to retrieve.
	 * @return array|mixed|null Meta data array, specific key value, or null when unavailable.
	 */
	public function get_import_meta( $key = null ) {
		$meta_file = $this->get_import_meta_file();
		if ( ! is_file( $meta_file ) ) {
			return array();
		}

		$meta = json_decode( file_get_contents( $meta_file ), true ); // phpcs:ignore WordPressVIP.Filesystem.ReadFile.Found,WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( ! is_array( $meta ) ) {
			return array();
		}

		if ( $key === null ) {
			return $meta;
		}

		if ( array_key_exists( $key, $meta ) ) {
			return $meta[ $key ];
		}

		return null;
	}

	/**
	 * Magic getter for demo fields.
	 *
	 * @param string $prop Property name.
	 * @return mixed|null
	 */
	public function __get( $prop ) {
		if ( array_key_exists( $prop, $this->fields ) ) {
			return $this->fields[ $prop ];
		}

		return null;
	}

	/**
	 * Magic setter for demo fields and known properties.
	 *
	 * Only allows setting keys that exist in {@see $this->fields} and
	 * casts values to the expected types. Attempts to set unknown
	 * properties are reported via _doing_it_wrong to aid debugging.
	 *
	 * @param string $name  Property name.
	 * @param mixed  $value Property value.
	 */
	public function __set( $name, $value ) {
		// If it's one of the declared fields, store it with proper casting.
		if ( array_key_exists( $name, $this->fields ) ) {
			switch ( $name ) {
				case 'include_attachments':
					$this->fields[ $name ] = (bool) $value;
					break;
				case 'attachments_batch':
					$this->fields[ $name ] = (int) $value;
					break;
				case 'required_plugins':
				case 'tags':
					$this->fields[ $name ] = (array) $value;
					break;
				default:
					$this->fields[ $name ] = $value;
			}
			return;
		}

		// Unknown property — report to help debugging.
		if ( function_exists( '_doing_it_wrong' ) ) {
			_doing_it_wrong( __CLASS__ . '::__set', sprintf( /* translators: %s: property name */ esc_html__( 'Attempt to set unknown property "%s" on Demo object.', 'better-block-editor' ), esc_html( $name ) ), '1.0' );
		}
	}

	/**
	 * Magic isset handler for demo fields and protected properties.
	 *
	 * Called when isset() or empty() is used on inaccessible properties.
	 *
	 * @param string $name Property name.
	 *
	 * @return bool
	 */
	public function __isset( $name ) {
		// Field exists in fields array.
		if ( array_key_exists( $name, $this->fields ) ) {
			return null !== $this->fields[ $name ];
		}

		// If a class property exists (protected/private), check it here.
		if ( property_exists( $this, $name ) ) {
			return isset( $this->$name );
		}

		return false;
	}

	/**
	 * Determines the current import status slug.
	 *
	 * @return string Import status identifier.
	 */
	protected function get_import_status() {
		$entire_demo_is_imported = $this->post_types_imported && $this->attachments_imported;
		if ( $entire_demo_is_imported ) {
			return static::DEMO_STATUS_FULL_IMPORT;
		}

		return static::DEMO_STATUS_NOT_IMPORTED;
	}

	/**
	 * Normalises raw demo fields and stores them locally.
	 *
	 * @param array $fields Raw demo field data.
	 * @return void
	 */
	protected function setup_fields( $fields ) {
		$allowed_fields = array(
			'title'               => '',
			'id'                  => '',
			'include_attachments' => false,
			'screenshot'          => '',
			'link'                => '',
			'attachments_batch'   => 999,
			'required_plugins'    => array(),
			'tags'                => array(),
		);

		$fields       = array_intersect_key( (array) $fields, $allowed_fields );
		$this->fields = wp_parse_args( $fields, $allowed_fields );
	}
}
