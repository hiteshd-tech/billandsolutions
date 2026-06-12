<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Bulk_Edit_Globals\Control;

use Elementor\Core\Kits\Controls\Repeater;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Kit_Repeater extends Repeater
{

    const CONTROL_TYPE = 'the7-global-style-repeater';

    /**
     * Get control type.
     * Retrieve the control type, in this case `global-style-repeater`.
     * @return string Control type.
     * @since  3.0.0
     * @access public
     */
    public function get_type()
    {
        return self::CONTROL_TYPE;
    }

    /**
     * Get repeater control default settings.
     * Retrieve the default settings of the repeater control. Used to return the
     * default settings while initializing the repeater control.
     * @return array Control default settings.
     * @since  3.0.0
     * @access protected
     */
    protected function get_default_settings()
    {
        $settings = parent::get_default_settings();

        $settings['item_actions']['bulk_action'] = false;

        return $settings;
    }

    /**
     * Render repeater control output in the editor.
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     * @since  3.0.0
     * @access public
     */
    public function content_template()
    {
        ?>
        <div class="elementor-repeater-header-wrapper">
            <div class="elementor-repeater-row-controls elementor-repeater-tool-bulk-action-select-all">
                <input class="elementor-repeater-row-tools" type="checkbox" id="elementor-choose-select-all" name="elementor-choose-select-all"
                       value="false">
                <div class="elementor-control-title">
                    <label class="choose-select-all-not-active" for="elementor-choose-select-all"><?php echo esc_html__('Select all', 'the7mk2') ?></label>
                    <label class="choose-select-all-active" for="elementor-choose-select-all"><?php echo esc_html__('Deselect all', 'the7mk2') ?></label>
                </div>
            </div>
        </div>
        <div class="elementor-repeater-fields-wrapper"></div>
        <# if ( itemActions.add ) { #>
        <div class="elementor-button-wrapper">
            <button class="elementor-button elementor-repeater-add" type="button">
                <i class="eicon-plus" aria-hidden="true"></i>
                <span class="elementor-repeater__add-button__text">{{{addButtonText }}}</span>
            </button>
        </div>
        <# } #>
        <?php
    }
}
