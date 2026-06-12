<?php

use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<script type="text/template" id="tmpl-elementor-template-library-templates">
	<#
	var activeSource = elementor.templates.getFilter('source');
	#>
	<div id="elementor-template-library-toolbar">
		<# if ( 'remote' === activeSource ) {
		var activeType = elementor.templates.getFilter('type');
		#>
		<div id="elementor-template-library-filter-toolbar-remote" class="elementor-template-library-filter-toolbar">
			<# if ( 'page' === activeType ) { #>
			<div id="elementor-template-library-order">
				<input type="radio" id="elementor-template-library-order-new" class="elementor-template-library-order-input" name="elementor-template-library-order" value="date">
				<label for="elementor-template-library-order-new" class="elementor-template-library-order-label"><?php echo esc_html__( 'New', 'elementor' ); ?></label>
				<input type="radio" id="elementor-template-library-order-trend" class="elementor-template-library-order-input" name="elementor-template-library-order" value="trendIndex">
				<label for="elementor-template-library-order-trend" class="elementor-template-library-order-label"><?php echo esc_html__( 'Trend', 'elementor' ); ?></label>
				<input type="radio" id="elementor-template-library-order-popular" class="elementor-template-library-order-input" name="elementor-template-library-order" value="popularityIndex">
				<label for="elementor-template-library-order-popular" class="elementor-template-library-order-label"><?php echo esc_html__( 'Popular', 'elementor' ); ?></label>
			</div>
			<# } else if ( 'the7-lb' !== activeType && 'lb' !== activeType ) {
			var config = elementor.templates.getConfig( activeType );
			if ( config.categories ) { #>
			<div id="elementor-template-library-filter">
				<select id="elementor-template-library-filter-subtype" class="elementor-template-library-filter-select" data-elementor-filter="subtype">
					<option></option>
					<# config.categories.forEach( function( category ) {
					var selected = category === elementor.templates.getFilter( 'subtype' ) ? ' selected' : '';
					#>
					<option value="{{ category }}"{{{ selected }}}>{{{ category }}}</option>
					<# } ); #>
				</select>
			</div>
			<# }
			} #>
			<div id="elementor-template-library-my-favorites">
				<# var checked = elementor.templates.getFilter( 'favorite' ) ? ' checked' : ''; #>
				<input id="elementor-template-library-filter-my-favorites" type="checkbox"{{{ checked }}}>
				<label id="elementor-template-library-filter-my-favorites-label" for="elementor-template-library-filter-my-favorites">
					<i class="eicon" aria-hidden="true"></i>
					<?php echo esc_html__( 'My Favorites', 'elementor' ); ?>
				</label>
			</div>
		</div>
		<# } else { #>
		<div id="elementor-template-library-filter-toolbar-local" class="elementor-template-library-filter-toolbar"></div>
		<# } #>
		<div id="elementor-template-library-filter-text-wrapper">
			<label for="elementor-template-library-filter-text" class="elementor-screen-only"><?php echo esc_html__( 'Search Templates:', 'elementor' ); ?></label>
			<input id="elementor-template-library-filter-text" placeholder="<?php echo esc_attr__( 'Search', 'elementor' ); ?>">
			<i class="eicon-search"></i>
		</div>
	</div>
	<# if ( 'local' === activeSource ) { #>
	<div id="elementor-template-library-order-toolbar-local">
		<div class="elementor-template-library-local-column-1">
			<input type="radio" id="elementor-template-library-order-local-title" class="elementor-template-library-order-input" name="elementor-template-library-order-local" value="title" data-default-ordering-direction="asc">
			<label for="elementor-template-library-order-local-title" class="elementor-template-library-order-label"><?php echo esc_html__( 'Name', 'elementor' ); ?></label>
		</div>
		<div class="elementor-template-library-local-column-2">
			<input type="radio" id="elementor-template-library-order-local-type" class="elementor-template-library-order-input" name="elementor-template-library-order-local" value="type" data-default-ordering-direction="asc">
			<label for="elementor-template-library-order-local-type" class="elementor-template-library-order-label"><?php echo esc_html__( 'Type', 'elementor' ); ?></label>
		</div>
		<div class="elementor-template-library-local-column-3">
			<input type="radio" id="elementor-template-library-order-local-author" class="elementor-template-library-order-input" name="elementor-template-library-order-local" value="author" data-default-ordering-direction="asc">
			<label for="elementor-template-library-order-local-author" class="elementor-template-library-order-label"><?php echo esc_html__( 'Created By', 'elementor' ); ?></label>
		</div>
		<div class="elementor-template-library-local-column-4">
			<input type="radio" id="elementor-template-library-order-local-date" class="elementor-template-library-order-input" name="elementor-template-library-order-local" value="date">
			<label for="elementor-template-library-order-local-date" class="elementor-template-library-order-label"><?php echo esc_html__( 'Creation Date', 'elementor' ); ?></label>
		</div>
		<div class="elementor-template-library-local-column-5">
			<div class="elementor-template-library-order-label"><?php echo esc_html__( 'Actions', 'elementor' ); ?></div>
		</div>
	</div>
	<# } #>
	<div id="elementor-template-library-templates-container"></div>
	<# if ( 'remote' === activeSource ) { #>
	<div id="elementor-template-library-footer-banner">
		<img class="elementor-nerd-box-icon" src="<?php
		Utils::print_unescaped_internal_string( ELEMENTOR_ASSETS_URL . 'images/information.svg' );
		?>" loading="lazy" />
		<div class="elementor-excerpt"><?php echo esc_html__( 'Stay tuned! More awesome templates coming real soon.', 'elementor' ); ?></div>
	</div>
	<# } #>
</script>

<script type="text/template" id="tmpl-elementor-template-library-template-remote">
	<div class="elementor-template-library-template-body">
		<?php // 'lp' stands for Landing Pages Library type. ?>
		<# if ( 'page' === type || 'lp' === type ) { #>
		<div class="elementor-template-library-template-screenshot" style="background-image: url({{ thumbnail }});"></div>
		<# } else { #>
		<img src="{{ thumbnail }}" loading="lazy">
		<# } #>
		<div class="elementor-template-library-template-preview">
			<i class="eicon-zoom-in-bold" aria-hidden="true"></i>
		</div>
	</div>
	<div class="elementor-template-library-template-footer">
		{{{ elementor.templates.layout.getTemplateActionButton( obj ) }}}
		<div class="elementor-template-library-template-name">{{{ title }}} - {{{ type }}}</div>
        <# if ( ! obj.the7_pro ) { #>
		<div class="elementor-template-library-favorite">
			<input id="elementor-template-library-template-{{ template_id }}-favorite-input" class="elementor-template-library-template-favorite-input" type="checkbox"{{ favorite ? " checked" : "" }}>
			<label for="elementor-template-library-template-{{ template_id }}-favorite-input" class="elementor-template-library-template-favorite-label">
				<i class="eicon-heart-o" aria-hidden="true"></i>
				<span class="elementor-screen-only"><?php echo esc_html__( 'Favorite', 'elementor' ); ?></span>
			</label>
		</div>
        <# } #>
	</div>
</script>


<script type="text/template" id="tmpl-elementor-template-library-the7-upgrade-plan-button">
    <a
            class="elementor-template-library-template-action elementor-button go-pro"
            href="{{{ promotionLink }}}"
            target="_blank"
    >
        <span class="elementor-button-title">{{{ promotionText }}}</span>
    </a>
</script>


<script type="text/template" id="tmpl-elementor-template-library-get-the7-insert-button">
    <a class="elementor-template-library-template-action elementor-template-library-template-insert elementor-button the7-active e-primary">
        <i class="eicon-file-download" aria-hidden="true"></i>
        <span class="elementor-button-title">
                <?php esc_html_e( 'Import', 'the7mk2' ); ?>
        </span>
    </a>
</script>

<script type="text/template" id="tmpl-elementor-template-library-get-the7-pro-insert-button">
	<?php if ( presscore_theme_is_activated() ) : ?>
        <a class="elementor-template-library-template-action elementor-template-library-template-insert elementor-button the7-active e-primary">
            <i class="eicon-file-download" aria-hidden="true"></i>
            <span class="elementor-button-title">
					<?php esc_html_e( 'Import', 'the7mk2' ); ?>
            </span>
        </a>
	<?php else : ?>
        <a class="elementor-template-library-template-action  elementor-button e-primary" href="https://themeforest.net/item/the7-responsive-multipurpose-wordpress-theme/5556590" target="_blank">
            <i class="eicon-file-download" aria-hidden="true"></i>
            <span class="elementor-button-title">
					<?php esc_html_e( 'Activate to Unlock', 'the7mk2' ); ?>
            </span>
        </a>
	<?php endif; ?>
</script>
