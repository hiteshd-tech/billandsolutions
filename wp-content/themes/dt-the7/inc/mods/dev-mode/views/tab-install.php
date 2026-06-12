<?php
/**
 * Install tab template.
 *
 * @package The7/Dev/Templates
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="card">
	<h2 class="title">Re-Install theme</h2>
	<p class="the7-subtitle">If you need to re-install theme, you can do so here:</p>
	<form action="update.php?action=upgrade-theme&the7-force-update=true" method="post" name="upgrade-themes">
		<?php wp_nonce_field( 'upgrade-theme_dt-the7' ); ?>
		<input type="hidden" name="theme" value="dt-the7">
		<?php
		$the7_remote_api = new \The7_Remote_API( presscore_get_purchase_code() );
		$versions        = $the7_remote_api->get_available_theme_versions();
		if ( $versions ) {
			echo '<select name="version" style="margin-right: 7px;">';
			echo '<option value="">latest</option>';
			foreach ( $versions as $version ) {
				echo '<option value="' . esc_attr( $version ) . '"' . selected( $version, THE7_VERSION, false ) . '>' . esc_html( $version ) . '</option>';
			}
			echo '</select>';
		}
		?>
		<button class="button button-primary" name="upgrade">Re-install The7</button>
	</form>
</div>
