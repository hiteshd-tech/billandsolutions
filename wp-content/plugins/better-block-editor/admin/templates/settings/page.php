<?php

use BetterBlockEditor\Core\Settings;

defined( 'ABSPATH' ) || exit; ?>

<?php
	$tabs  = array(
		Settings::TAB_FEATURES => array(
			'label' => __( 'Features', 'better-block-editor' ),
		),
		Settings::TAB_BREAKPOINTS => array(
			'label' => __( 'Breakpoints', 'better-block-editor' ),
		),
		Settings::TAB_DESIGN => array(
			'label' => __( 'Design System', 'better-block-editor' ),
		),
		Settings::TAB_BLOCKS => array(
			'label' => __( 'Blocks', 'better-block-editor' ),
		),
	);
	//Allow adding custom tabs
	$tabs = apply_filters('wpbbe-settings_tabs', $tabs);
?>

<div class="wrap">
	<h1><?php echo esc_html( __( 'Better Block Editor', 'better-block-editor' ) ); ?></h1>
	<nav class="nav-tab-wrapper wpbbe-tabs">
		<?php foreach ( $tabs as $tab_slug => $tab ) : ?>
			<a href="#"
			   class="nav-tab"
			   data-tab="<?php echo esc_attr( $tab_slug ); ?>">
				<?php echo esc_html( $tab['label'] ); ?>
			</a>
		<?php endforeach; ?>
	</nav>
	<form action="options.php" method="post">
		<?php
		settings_fields( WPBBE_PLUGIN_ID . '_settings' );
		do_settings_sections( Settings::MENU_PAGE_SLUG );
		submit_button( esc_attr( __( 'Save Settings', 'better-block-editor' ) ) );
		?>
	</form>
</div>
