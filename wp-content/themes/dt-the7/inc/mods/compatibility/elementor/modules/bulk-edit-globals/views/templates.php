<?php

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>

<script type="text/template" id="tmpl-the7-bulk-edit-globals-content">
    <div id="the7-bulk-edit-globals-content-controls"></div>


    <div id="elementor-finder__search">
        <i class="eicon-search" aria-hidden="true"></i>
        <input id="elementor-finder__search__input" placeholder="<?php echo esc_attr__( 'Type to find anything in Elementor', 'elementor' ); ?>" autocomplete="off">
    </div>
    <div id="the7-bulk-edit-globals__content"></div>
</script>


<script type="text/template" id="tmpl-the7-elementor-global-style-repeater-row">
    <# let removeClass = 'remove',
    removeIcon = 'eicon-trash-o';

    if ( ! itemActions.remove ) {
    removeClass += '--disabled';

    removeIcon = 'eicon-disable-trash-o'
    }
    #>
    <# if ( itemActions.sort ) { #>
    <button class="elementor-repeater-row-tool elementor-repeater-row-tools elementor-repeater-tool-sort">
        <i class="eicon-cursor-move" aria-hidden="true"></i>
        <span class="elementor-screen-only"><?php echo esc_html__( 'Reorder', 'elementor' ); ?></span>
    </button>
    <# } #>

    <button class="elementor-repeater-row-tool elementor-repeater-tool-{{{ removeClass }}}">
        <i class="{{{ removeIcon }}}" aria-hidden="true"></i>
        <# if ( itemActions.remove ) { #>
        <span class="elementor-screen-only"><?php echo esc_html__( 'Remove', 'elementor' ); ?></span>
        <# } #>
    </button>
    <div class="elementor-repeater-row-controls">
        <# if ( itemActions.bulk_action ) { #>
        <input class="elementor-repeater-row-tools elementor-repeater-tool-bulk-action" type="checkbox" name="elementor-choose-{{ _id }}" value="false">
        <# } #>
    </div>
</script>