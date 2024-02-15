<?php

/**
 * @since V2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 *
 */
final class BBToolbox_SettingButtonLocation
{
    /**
     * @var string
     */
    static var $field = 'BB_Toolbox_location';

    static var $val_menu = 'menu';
    static var $val_topbar = 'topbar';

    static public function activate()
    {
//        register_setting( string $option_group, string $option_name, array $args = array() )
    }

    /**
     * @return void
     */
    static public function uninstall(){
        delete_option( self::$field );
    }
}
