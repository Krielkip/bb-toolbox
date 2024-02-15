<?php

/**
 * ToDo:
 * - Rebuild BB-Toolbox.php
 * - Add Plugin https://wordpress.org/plugins/excerpt-editor-for-beaver-builder/ in to this plugin (and making it EOL)
 * - Add Easy Plugin support for more modules
 * - Add Support for additional settings
 * - Add Support for Location for UI button. (and shorter name or just a icon.. i prefer the icon <3)
 * - Test it on the new BeaverBuilder
 * - Bake a cake.
 */

class BBToolbox
{

    protected $loader;
    protected $plugin_name;
    protected $version;

    /**
     *
     */
    public function __construct() {
        if ( defined( 'BBTOOLBOX_VERSION' ) ) {
            $this->version = BBTOOLBOX_VERSION;
        } else {
            $this->version = '2.0.0';
        }
        $this->plugin_name = 'BB-Toolbox';

        $this->load_dependencies();
        $this->set_locale();
//        $this->define_admin_hooks();
//        $this->define_public_hooks();

    }

    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/BBToolboxLoader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/settings/BBToolbox_i18n.php';

//        /**
//         * The class responsible for defining all actions that occur in the admin area.
//         */
//        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-plugin-name-admin.php';
//
//        /**
//         * The class responsible for defining all actions that occur in the public-facing
//         * side of the site.
//         */
//        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-plugin-name-public.php';

        $this->loader = new BBToolboxLoader();

    }

    private function set_locale() {

        $plugin_i18n = new BBToolbox_i18n();

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

    }
    private function define_admin_hooks() {

        $plugin_admin = new Plugin_Name_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }
}