<?php

namespace The7\Admin\Notices;

defined( 'ABSPATH' ) || exit;

class The7_Block_Editor_Obsolete extends Abstract_Notice {

	public function render() {
		?>

		<p role="heading"><?php esc_html_e( 'The7 Block Editor is obsolete', 'the7mk2' ); ?></p>
		<p>
			<?php esc_html_e( 'All its features have been transferred to the Better Block Editor (BBE) and BBE Plus plugins.', 'the7mk2' ); ?>
		</p>
		<p>
			<a id="the7-block-editor-obsolete-run-migration" href="#">
				<?php esc_html_e( 'Install and run the database update', 'the7mk2' ); ?>
			</a>
		</p>

		<form id="the7-block-editor-obsolete-migration-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" class="hidden">
			<?php wp_nonce_field( 'the7-dev-tools' ); ?>
			<input type="hidden" name="action" value="the7_use_dev_tool">
			<input type="hidden" name="migration" value="12.10.0">
			<button type="submit" class="button" name="tool" value="run_migration"><?php esc_html_e( 'Migrate', 'the7mk2' ); ?></button>
		</form>

		<script type="text/javascript">
			document.getElementById('the7-block-editor-obsolete-run-migration').addEventListener('click', function(e) {
				e.preventDefault();
				document.querySelector('#the7-block-editor-obsolete-migration-form button[type="submit"]').click();
			});
		</script>

		<?php
	}

	public function get_code() {
		return 'the7_block_editor_obsolete';
	}

	public function is_visible() {
		if ( ! current_user_can( 'update_plugins' ) ) {
			return false;
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active( 'the7-block-editor/the7-block-editor.php' );
	}

	public function get_wrapper_class() {
		return 'the7-dashboard-notice notice-warning the7-debug-notice';
	}
}
