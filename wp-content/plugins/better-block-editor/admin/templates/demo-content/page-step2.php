<?php

use BetterBlockEditor\Modules\DemoContent\ActionBuilders\ImportContentActionBuilder;
use BetterBlockEditor\Modules\DemoContent\Demo\Factory as DemoFactory;
use BetterBlockEditor\Modules\DemoContent\Module;

defined( 'ABSPATH' ) || exit;

if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'wpbbe_import_demo' ) ) {
	echo '<div class="notice notice-error"><p>' . esc_html__( 'Invalid request. Please try again.', 'better-block-editor' ) . '</p></div>';
	return;
}

if ( ! current_user_can( Module::CAPABILITY ) ) {
	echo '<div class="notice notice-error"><p>' . esc_html__( 'You do not have permission to perform this action.', 'better-block-editor' ) . '</p></div>';
	return;
}

$demo_id        = sanitize_key( wp_unslash( $_POST['demo_id'] ?? '' ) );
$demo           = DemoFactory::create( $demo_id );
$action_builder = new ImportContentActionBuilder( $demo, $_POST );

$action_builder->localize_data_to_js();

if ( ! empty( $action_builder->get_error() ) ) {
	echo wp_kses_post( $action_builder->get_error() );

	return;
}
?>

<div class="bbe-import-feedback">
	<?php echo wp_kses_post( $action_builder->get_starting_text() ); ?>
</div>
<div class="bbe-go-back-link hide-if-js">
	<p>
		<?php echo esc_html__( 'All done.', 'better-block-editor' ); ?>
	</p>
	<p>
		<?php
		echo '<a id="bbe-demo-visit-site-link" href="' . esc_url( home_url() ) . '">' . esc_html__( 'Visit site', 'better-block-editor' ) . '</a>';
		echo ' | ';
		echo '<a href="' . esc_url( BetterBlockEditor\Modules\DemoContent\Module::get_admin_url() ) . '">' . esc_html__( 'Back to Site Templates', 'better-block-editor' ) . '</a>';
		?>
	</p>
</div>
