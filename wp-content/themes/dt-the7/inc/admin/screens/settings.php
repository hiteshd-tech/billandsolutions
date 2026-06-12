<?php
/**
 * Dashboard settings page.
 *
 * @package The7
 */

defined( 'ABSPATH' ) || exit;
?>

<div id="the7-dashboard" class="wrap">

	<h1><?php esc_html_e( 'Theme Settings', 'the7mk2' ); ?></h1>
	<div class="wp-header-end"></div>

	<?php require __DIR__ . '/partials/settings/settings.php'; ?>

</div>