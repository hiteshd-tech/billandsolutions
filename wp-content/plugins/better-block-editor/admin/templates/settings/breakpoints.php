<?php defined( 'ABSPATH' ) || exit; ?>

<div>
	<div id="user-defined-breakpoint-list">
	</div>

	<button
		type="button"
		class="button button-secondary"
		onclick="window.wpbbeSettingsAddBreakpoint(event)"
	>
		<span
			class="dashicons dashicons-plus"
			style="width: auto; height: auto; font-size: 1.2em; vertical-align: middle;"
			title="<?php echo esc_attr( __( 'Add breakpoint', 'better-block-editor' ) ); ?>"
		/>
	</button>
</div>
