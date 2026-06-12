<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Bulk_Edit_Globals\Control;

use Elementor\Control_Switcher;

class Kit_Switcher extends Control_Switcher
{
    const CONTROL_TYPE = 'the7-global-style-switcher';

    /**
     * Get control type.
     *
     * Retrieve the control type, in this case `global-style-switcher`.
     *
     * @since 3.13.0
     * @access public
     *
     * @return string Control type.
     */
    public function get_type() {
        return self::CONTROL_TYPE;
    }
}