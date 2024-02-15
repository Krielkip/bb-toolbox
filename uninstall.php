<?php
/**
 * @since V2.0.0
 */

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/settings/BBToolbox_SettingButtonLocation.php';

BBToolbox_SettingButtonLocation::uninstall();


