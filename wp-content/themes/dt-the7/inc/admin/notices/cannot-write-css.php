<?php
namespace The7\Admin\Notices;

defined( 'ABSPATH' ) || exit;

class Cannot_Write_CSS extends Abstract_Notice {

	public function render() {
		?>

		<p role="heading"><?php echo esc_html_x( 'Failed to create customization CSS file', 'admin', 'the7mk2' ); ?></p>
		<p>
			<?php echo esc_html_x( 'The “…/wp-content/uploads/” folder is not writable. Please ensure it exists and that its CHMOD is set to 755.', 'admin', 'the7mk2' ); ?>
		</p>

		<?php
	}

	public function get_code() {
		return 'unable-to-write-css';
	}

	public function is_visible() {
		global $current_screen;

		return function_exists( 'optionsframework_get_options_files' )
		&& optionsframework_get_options_files( $current_screen->parent_base )
		&& ! get_option( 'presscore_less_css_is_writable', 1 );
	}

	public function get_wrapper_class() {
		return self::NOTICE_ERROR;
	}
}
