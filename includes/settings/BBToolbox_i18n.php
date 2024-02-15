<?php

class BBToolbox_i18n
{
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'bb-toolbox',
            false,
            dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
        );

    }
}