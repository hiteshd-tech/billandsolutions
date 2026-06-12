<?php
/**
 * Demo content admin page.
 *
 * @package The7\Admin\Pages
 */

namespace The7\Admin\Pages;

defined( 'ABSPATH' ) || exit;

class Demo_Content extends Admin_Page {

	public function __construct() {
		$this->slug       = 'the7-demo-content';
		$this->title      = __( 'Website Templates', 'the7mk2' );
		$this->capability = 'switch_themes';
	}

	/**
	 * Render page content.
	 */
	public function render() {
		$the7_dashboard_url = admin_url( 'admin.php?page=the7-dashboard' ) . '#pre-made-websites';
		?>

		<script type="text/javascript">
			// Redirect to the dashboard page.
			window.location.href = '<?php echo esc_url( $the7_dashboard_url ); ?>';
		</script>

		<div id="the7-dashboard" class="wrap">
			<h1><?php esc_html_e( 'Pre-made Websites', 'the7mk2' ); ?></h1>
			<div class="wp-header-end"></div>

			<p>
				<a href="<?php echo esc_url( $the7_dashboard_url ); ?>">
					<?php esc_html_e( 'Go to Website Templates', 'the7mk2' ); ?>
				</a>
			</p>
		</div>

		<?php
	}
}
