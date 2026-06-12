<?php
/**
 * The7 system status info.
 *
 * @package The7
 */

namespace The7\Admin;

use WP_Site_Health;

defined( 'ABSPATH' ) || exit;

/**
 * Class Status_Info
 */
class Status_Info {

	/**
	 * Return system status information.
	 *
	 * @return array
	 */
	public static function get_system_status() {
		return [
			'install_location'     => self::check_install_location(),
			'fs_accessible'        => self::check_fs_accessible(),
			'uploads_writable'     => self::check_uploads_writable(),
			'zip_archive'          => self::check_zip_archive(),
			'php_version'          => self::check_php_version(),
			'php_max_input_vars'   => self::check_php_max_input_vars(),
			'wp_memory_limit'      => self::check_wp_memory_limit(),
			'php_time_limit'       => self::check_php_time_limit(),
			'php_modules'          => self::check_php_modules(),
		];
	}

	/**
	 * Check install location.
	 *
	 * @return array
	 */
	private static function check_install_location() {
		$template_name = 'dt-the7';
		$is_standard   = get_template() === $template_name;

		if ( $is_standard ) {
			return [
				'label'       => esc_html__( 'Install Location:', 'the7mk2' ),
				'status'      => 'good',
				'value'       => esc_html__( 'Standard', 'the7mk2' ),
				'description' => '',
			];
		}

		return [
			'label'       => esc_html__( 'Install Location:', 'the7mk2' ),
			'status'      => 'bad',
			'value'       => esc_html__( 'Non-standard', 'the7mk2' ),
			'description' => sprintf(
				// translators: %s - theme folder name.
				esc_html__( 'Using The7 from non-standard install location or having a different directory name could lead to issues in receiving and installing updates. Please make sure that theme folder name is %s, without spaces.', 'the7mk2' ),
				'<strong>' . esc_html( $template_name ) . '</strong>'
			),
		];
	}

	/**
	 * Check file system accessibility.
	 *
	 * @return array
	 */
	private static function check_fs_accessible() {
		global $wp_filesystem;

		if ( $wp_filesystem || WP_Filesystem() ) {
			return [
				'label'       => esc_html__( 'File System Accessible:', 'the7mk2' ),
				'status'      => 'good',
				'value'       => esc_html__( 'Yes', 'the7mk2' ),
				'description' => '',
			];
		}

		return [
			'label'       => esc_html__( 'File System Accessible:', 'the7mk2' ),
			'status'      => 'bad',
			'value'       => esc_html__( 'No', 'the7mk2' ),
			'description' => sprintf(
				// translators: %1$s - config file, %2$s - code, %3$s - before text.
				esc_html__( 'Theme has no direct access to the file system. Therefore plugins and pre-made websites installation is not possible. Please try to insert the following code in %1$s: %2$s before %3$s', 'the7mk2' ),
				'<code>wp-config.php</code>',
				'<code>define( "FS_METHOD", "direct" );</code>',
				'<code>/* That\'s all, stop editing! Happy blogging. */</code>.'
			),
		];
	}

	/**
	 * Check uploads folder writability.
	 *
	 * @return array
	 */
	private static function check_uploads_writable() {
		$wp_uploads = wp_get_upload_dir();
		if ( wp_is_writable( $wp_uploads['basedir'] . '/' ) ) {
			return [
				'label'       => esc_html__( 'Uploads Folder Writable:', 'the7mk2' ),
				'status'      => 'good',
				'value'       => esc_html__( 'Yes', 'the7mk2' ),
				'description' => '',
			];
		}

		return [
			'label'       => esc_html__( 'Uploads Folder Writable:', 'the7mk2' ),
			'status'      => 'bad',
			'value'       => esc_html__( 'No', 'the7mk2' ),
			'description' => esc_html__( 'Uploads folder must be writable to allow WordPress function properly.', 'the7mk2' ) . '<br><span class="the7-tip">' . sprintf(
				// translators: %s - link to wp codex article.
				esc_html__( 'See %s or contact your hosting provider.', 'the7mk2' ),
				'<a href="https://developer.wordpress.org/advanced-administration/server/file-permissions/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'changing file permissions', 'the7mk2' ) . '</a>'
			) . '</span>',
		];
	}

	/**
	 * Check ZipArchive support.
	 *
	 * @return array
	 */
	private static function check_zip_archive() {
		if ( class_exists( 'ZipArchive' ) ) {
			return [
				'label'       => esc_html__( 'ZipArchive Support:', 'the7mk2' ),
				'status'      => 'good',
				'value'       => esc_html__( 'Yes', 'the7mk2' ),
				'description' => '',
			];
		}

		return [
			'label'       => esc_html__( 'ZipArchive Support:', 'the7mk2' ),
			'status'      => 'bad',
			'value'       => esc_html__( 'No', 'the7mk2' ),
			'description' => esc_html__( 'ZipArchive is required for Icons Manager to work properly.', 'the7mk2' ) . '<br><span class="the7-tip">' . esc_html__( 'You may want to contact your hosting provider.', 'the7mk2' ) . '</span>',
		];
	}

	/**
	 * Check PHP version.
	 *
	 * @return array
	 */
	private static function check_php_version() {
		$php_version = PHP_VERSION;
		if ( version_compare( '7.2.0', $php_version, '>' ) ) {
			return [
				'label'       => esc_html__( 'PHP Version:', 'the7mk2' ),
				'status'      => 'okay',
				'value'       => esc_html( $php_version ),
				'description' => sprintf(
					// translators: %s - recommended php version.
					esc_html__( 'Current version is sufficient. However %s or greater is recommended to improve the performance.', 'the7mk2' ),
					'<strong>v.7.2.0</strong>'
				),
			];
		}

		return [
			'label'       => esc_html__( 'PHP Version:', 'the7mk2' ),
			'status'      => 'good',
			'value'       => esc_html( $php_version ),
			'description' => esc_html__( 'Current version is sufficient.', 'the7mk2' ),
		];
	}

	/**
	 * Check PHP Max Input Vars.
	 *
	 * @return array
	 */
	private static function check_php_max_input_vars() {
		$max_input_vars = ini_get( 'max_input_vars' );
		if ( $max_input_vars < 1000 ) {
			return [
				'label'       => esc_html__( 'PHP Max Input Vars:', 'the7mk2' ),
				'status'      => 'bad',
				'value'       => esc_html( $max_input_vars ),
				'description' => sprintf(
					// translators: %1$s - minimum value, %2$s - recommended value, %3$s - more value.
					esc_html__( 'Minimum value is %1$s. %2$s is recommended. %3$s or more may be required if lots of plugins are in use and/or you have a large amount of menu items.', 'the7mk2' ),
					'<strong>1000</strong>',
					'<strong>2000</strong>',
					'<strong>3000</strong>'
				),
			];
		}
		if ( $max_input_vars < 2000 ) {
			return [
				'label'       => esc_html__( 'PHP Max Input Vars:', 'the7mk2' ),
				'status'      => 'okay',
				'value'       => esc_html( $max_input_vars ),
				'description' => sprintf(
					// translators: %1$s - recommended value, %2$s - more value.
					esc_html__( 'Current limit is sufficient for most tasks. %1$s is recommended. %2$s or more may be required if lots of plugins are in use and/or you have a large amount of menu items.', 'the7mk2' ),
					'<strong>2000</strong>',
					'<strong>3000</strong>'
				),
			];
		}
		if ( $max_input_vars < 3000 ) {
			return [
				'label'       => esc_html__( 'PHP Max Input Vars:', 'the7mk2' ),
				'status'      => 'good',
				'value'       => esc_html( $max_input_vars ),
				'description' => sprintf(
					// translators: %s - more value.
					esc_html__( 'Current limit is sufficient. However, up to %s or more may be required if lots of plugins are in use and/or you have a large amount of menu items.', 'the7mk2' ),
					'<strong>3000</strong>'
				),
			];
		}

		return [
			'label'       => esc_html__( 'PHP Max Input Vars:', 'the7mk2' ),
			'status'      => 'good',
			'value'       => esc_html( $max_input_vars ),
			'description' => esc_html__( 'Current limit is sufficient.', 'the7mk2' ),
		];
	}

	/**
	 * Check WP Memory Limit.
	 *
	 * @return array
	 */
	private static function check_wp_memory_limit() {
		$memory    = presscore_get_wp_memory_limit();
		$hr_memory = size_format( $memory );

		$tip  = '<br><span class="the7-tip">';
		$tip .= sprintf(
			// translators: %s - wp codex article link.
			esc_html__( 'See %s or contact your hosting provider.', 'the7mk2' ),
			'<a href="https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#increasing-memory-allocated-to-php" target="_blank" rel="noopener noreferrer">' . esc_html__( 'increasing memory allocated to PHP', 'the7mk2' ) . '</a>'
		);
		$tip .= '</span>';

		if ( $memory < 67108864 ) {
			return [
				'label'       => esc_html__( 'WP Memory Limit:', 'the7mk2' ),
				'status'      => 'bad',
				'value'       => esc_html( $hr_memory ),
				'description' => sprintf(
					// translators: %1$s - minimum value, %2$s - recommended value, %3$s - more value.
					esc_html__( 'Minimum value is %1$s. %2$s is recommended. %3$s or more may be required if lots of plugins are in use and/or you want to install the Main Demo.', 'the7mk2' ),
					'<strong>64 MB</strong>',
					'<strong>128 MB</strong>',
					'<strong>256 MB</strong>'
				) . $tip,
			];
		}
		if ( $memory < 134217728 ) {
			return [
				'label'       => esc_html__( 'WP Memory Limit:', 'the7mk2' ),
				'status'      => 'okay',
				'value'       => esc_html( $hr_memory ),
				'description' => sprintf(
					// translators: %1$s - recommended value, %2$s - more value.
					esc_html__( 'Current memory limit is sufficient for most tasks. However, recommended value is %1$s. %2$s or more may be required if lots of plugins are in use and/or you want to install the Main Demo.', 'the7mk2' ),
					'<strong>128 MB</strong>',
					'<strong>256 MB</strong>'
				) . $tip,
			];
		}
		if ( $memory < 268435456 ) {
			return [
				'label'       => esc_html__( 'WP Memory Limit:', 'the7mk2' ),
				'status'      => 'good',
				'value'       => esc_html( $hr_memory ),
				'description' => sprintf(
					// translators: %s - more value.
					esc_html__( 'Current memory limit is sufficient for most tasks. However, %s or more may be required if lots of plugins are in use and/or you want to install the Main Demo.', 'the7mk2' ),
					'<strong>256 MB</strong>'
				) . $tip,
			];
		}

		return [
			'label'       => esc_html__( 'WP Memory Limit:', 'the7mk2' ),
			'status'      => 'good',
			'value'       => esc_html( $hr_memory ),
			'description' => esc_html__( 'Current memory limit is sufficient.', 'the7mk2' ),
		];
	}

	/**
	 * Check PHP Time Limit.
	 *
	 * @return array|null
	 */
	private static function check_php_time_limit() {
		if ( ! function_exists( 'ini_get' ) ) {
			return null;
		}

		$time_limit = (int) ini_get( 'max_execution_time' );

		$tip  = '<br><span class="the7-tip">';
		$tip .= sprintf(
			// translators: %s - wp codex article link.
			esc_html__( 'See %s or contact your hosting provider.', 'the7mk2' ),
			'<a href="https://developer.wordpress.org/advanced-administration/wordpress/common-errors/#php-errors" target="_blank" rel="noopener noreferrer">' . esc_html__( 'increasing max PHP execution time', 'the7mk2' ) . '</a>'
		);
		$tip .= '</span>';

		if ( $time_limit === 0 ) {
			return [
				'label'       => esc_html__( 'PHP Time Limit:', 'the7mk2' ),
				'status'      => 'good',
				'value'       => 'unlimited',
				'description' => esc_html__( 'Current time limit is sufficient.', 'the7mk2' ),
			];
		}
		if ( $time_limit < 30 ) {
			return [
				'label'       => esc_html__( 'PHP Time Limit:', 'the7mk2' ),
				'status'      => 'bad',
				'value'       => esc_html( $time_limit ),
				'description' => sprintf(
					// translators: %1$s - minimum value, %2$s - recommended value, %3$s - more value.
					esc_html__( 'Minimum value is %1$s. %2$s is recommended. Up to %3$s seconds may be required to install the Main Demo.', 'the7mk2' ),
					'<strong>30</strong>',
					'<strong>60</strong>',
					'<strong>300</strong>'
				) . $tip,
			];
		}
		if ( $time_limit < 60 ) {
			return [
				'label'       => esc_html__( 'PHP Time Limit:', 'the7mk2' ),
				'status'      => 'okay',
				'value'       => esc_html( $time_limit ),
				'description' => sprintf(
					// translators: %1$s - recommended value, %2$s - more value.
					esc_html__( 'Current time limit is sufficient for most tasks. However, recommended value is %1$s. Up to %2$s seconds may be required to install the Main Demo.', 'the7mk2' ),
					'<strong>60</strong>',
					'<strong>300</strong>'
				) . $tip,
			];
		}
		if ( $time_limit < 300 ) {
			return [
				'label'       => esc_html__( 'PHP Time Limit:', 'the7mk2' ),
				'status'      => 'good',
				'value'       => esc_html( $time_limit ),
				'description' => sprintf(
					// translators: %s - more value.
					esc_html__( 'Current time limit is sufficient. However, up to %s seconds may be required to install the Main Demo.', 'the7mk2' ),
					'<strong>300</strong>'
				) . $tip,
			];
		}

		return [
			'label'       => esc_html__( 'PHP Time Limit:', 'the7mk2' ),
			'status'      => 'good',
			'value'       => esc_html( $time_limit ),
			'description' => esc_html__( 'Current time limit is sufficient.', 'the7mk2' ),
		];
	}

	/**
	 * Check PHP Modules via WP_Site_Health.
	 *
	 * @return array|null
	 */
	private static function check_php_modules() {
		if ( ! class_exists( 'WP_Site_Health' ) || ! method_exists( 'WP_Site_Health', 'get_test_php_extensions' ) ) {
			return null;
		}

		$result = WP_Site_Health::get_instance()->get_test_php_extensions();
		if ( ! isset( $result['status'] ) || $result['status'] === 'good' ) {
			return null;
		}

		$class = 'status-okay';
		if ( $result['status'] === 'critical' ) {
			$class = 'status-bad';
		}

		// Transform description to include icons as in original code.
		$description = wp_kses_post( str_replace( [ 'warning', 'error' ], [ 'warning dashicons-info', 'error dashicons-warning' ], $result['description'] ) );

		return [
			'label'       => esc_html__( 'PHP Modules:', 'the7mk2' ),
			'status'      => $result['status'] === 'critical' ? 'bad' : 'okay',
			'value'       => esc_html( $result['status'] ),
			'value_class' => $class,
			'description' => esc_html( $result['label'] . '.' ) . $description,
			'is_php_modules' => true, // Flag for special styling if needed
		];
	}
}
