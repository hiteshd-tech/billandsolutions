<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="wrap">

	<h1><?php esc_html_e( 'Pre-made Website Templates', 'better-block-editor' ); ?></h1>

	<?php
	if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'wpbbe_import_demo' ) && isset( $_GET['step'] ) && absint( wp_unslash( $_GET['step'] ) ) === 2 ) {
		require __DIR__ . '/page-step2.php';
	} else {
		require __DIR__ . '/page-step1.php';
	}
	?>

</div>
