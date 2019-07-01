<?php
/**
 * Plugin Name: Toolbox for Beaver Builder
 * Plugin URI: http://www.particulare.nl
 * Description: Adds the toolbox to the Page Builder of Beaver Builder (lite). You can edit the page title, page permalink, page parent & page template. Also you can edit SEO Title + SEO Description  (WordPress SEO, All in one SEO, HeadSpace2 SEO, Platinum SEO Pack, SEO Framework or Genesis)
 * Version: 1.1.2
 * Author: Jack Krielen
 * Author URI: http://www.jackkrielen.nl
 * Copyright: (c) 2018 Particulare
 * License: GNU General Public License v2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bb-toolbox
 * Domain Path: /languages
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

final class BB_Addon_Toolbox
{

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     * @var object
     */
    public static $instance;

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        /* Constants */
        $this->define_constants();


        /* Hooks */
        $this->init_hooks();

    }

    /**
     * Define BB Addons constants.
     *
     * @since 1.0.0
     * @return void
     */
    private function define_constants()
    {
        define('BB_Toolbox_DIR', plugin_dir_path(__FILE__));
        define('BB_Toolbox_URL', plugins_url('/', __FILE__));
        define('BB_Toolbox_PATH', plugin_basename(__FILE__));
    }

    /**
     * Initializes actions and filters.
     *
     * @since 1.0.0
     * @return void
     */
    public function init_hooks()
    {
        add_action('init', array($this, 'load_toolbox'));
        add_action('plugins_loaded', array($this, 'loader'));
        add_action('wp_enqueue_scripts', array($this, 'load_scripts'), 100);
        add_action('admin_notices', array($this, 'admin_notices'));
        add_action('network_admin_notices', array($this, 'admin_notices'));
    }

    /**
     * Check on the right SEO Plugin
     * @param $plugins
     * @return bool
     */
    static public function has_seo_plugin($plugins)
    {
        /** Check for classes */
        if (isset($plugins['classes'])) {
            foreach ($plugins['classes'] as $name) {
                if (class_exists($name)) {
                    return TRUE;
                }
            }
        }

        /** Check for functions */
        if (isset($plugins['functions'])) {
            foreach ($plugins['functions'] as $name) {
                if (function_exists($name)) {
                    return TRUE;
                }
            }
        }

        /** Check for constants */
        if (isset($plugins['constants'])) {
            foreach ($plugins['constants'] as $name) {
                if (defined($name)) {
                    return TRUE;
                }
            }
        }

        return FALSE;
    }

    /**
     * List of SEO Plugins
     * @return mixed|void
     */
    public static function detect_seo_plugins()
    {

        return (
            // Use this filter to adjust plugin tests.
        apply_filters(
            'bb_toolbox_detect_seo_plugins',
            /** Add to this array to add new plugin checks. */
            array(

                // Classes to detect.
                'classes' => array(
                    'All_in_One_SEO_Pack',
                    'All_in_One_SEO_Pack_p',
                    'HeadSpace_Plugin',
                    'Platinum_SEO_Pack',
                    'wpSEO',
                    'Smartcrawl_Init'
                ),

                // Functions to detect.
                'functions' => array(
                    'genesis_constants',
                    'seopress_init' // SeoPress
                ),

                // Constants to detect.
                'constants' => array(
                    'WPSEO_VERSION',
                    'SEOPRESS_VERSION' //SeoPress
                ),
            )
        )
        );
    }

    /**
     * Get correct SEO attributes & content
     * @param $post
     * @return array
     */
    static public function get_seo_plugin($post)
    {
        if (self::has_seo_plugin(array(
            'classes' => array(
                'All_in_One_SEO_Pack',
                'All_in_One_SEO_Pack_p'
            )
        ))) {
            // All in one SEO : aiosp_title + aiosp_description
            $meta_title_field = '_aioseop_title';
            $meta_description_field = '_aioseop_description';
        } else if (self::has_seo_plugin(array(
            'classes' => array('wpSEO'),
            'constants' => array('WPSEO_VERSION')
        ))) {
            //  WordPress SEO
            $meta_title_field = '_yoast_wpseo_title';
            $meta_description_field = '_yoast_wpseo_metadesc';
        } else if (self::has_seo_plugin(array('classes' => array('HeadSpace_Plugin')))) {
            //  HeadSpace2 SEO
            $meta_title_field = '_headspace_page_title';
            $meta_description_field = '_headspace_description';
        } else if (self::has_seo_plugin(array('classes' => array('Platinum_SEO_Pack')))) {
            //  Platinum SEO Pack
            $meta_title_field = 'title';
            $meta_description_field = 'description';
        } else if (self::has_seo_plugin(array('functions' => array('genesis_constants'))) || self::has_seo_plugin(array('classes' => array('The_SEO_Framework_Load')))) {
            //  Genesis + SEO Framework
            $meta_title_field = '_genesis_title';
            $meta_description_field = '_genesis_description';
        } else if (self::has_seo_plugin(array(
            'functions' => array('seopress_init'),
            'constants' => array('SEOPRESS_VERSION')
        ))) {
            $meta_title_field = '_seopress_titles_title';
            $meta_description_field = '_seopress_titles_desc';
        } else if (self::has_seo_plugin(array('classes' => array('Smartcrawl_Init')))) {
            $meta_title_field = '_wds_title';
            $meta_description_field = '_wds_metadesc';
        }

        $meta_title = get_post_meta($post->ID, $meta_title_field, TRUE);
        $meta_description = get_post_meta($post->ID, $meta_description_field, TRUE);
        return array(
            $meta_title,
            $meta_description,
            $meta_title_field,
            $meta_description_field
        );
    }


    /**
     * Rebuild of the native Wordpress wp_dropdown_pages to only show array/
     * @param string $args
     * @return array|mixed|void
     */
    static public function get_dropdown_pages($args = '')
    {
        $defaults = array(
            'depth' => 0,
            'child_of' => 0,
            'value_field' => 'ID',
        );

        $args = wp_parse_args($args, $defaults);

        $pages = get_pages($args);
        $array = array();
        if (!empty($pages)) {
            $depth = 0;
            $parent = 0;
            foreach ($pages as $page) {
                if ($page->post_parent !== 0 && $page->post_parent !== $parent) {
                    $depth++;
                    $parent = $page->post_parent;
                } else {
                    if ($depth !== 0 && $page->post_parent === 0) {
                        $depth = 0;
                        $parent = 0;
                    }
                }
                $array[$page->ID] = str_repeat('-', $depth) . ' ' . $page->post_title;
            }
        }

        /**
         * Filters the HTML output of a list of pages as a drop down.
         *
         * @since 2.1.0
         * @since 4.4.0 `$args` and `$pages` added as arguments.
         *
         * @param string $array HTML output for drop down list of pages.
         * @param array $args The parsed arguments array.
         * @param array $pages List of WP_Post objects returned by `get_pages()`
         */
        $array = apply_filters('bb_toolbox_dropdown_pages', $array, $args, $pages);
        return $array;
    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The BB_Addon_excerpt object.
     */
    public static function get_instance()
    {
        if (!isset(self::$instance) && !(self::$instance instanceof BB_Addon_Toolbox)) {
            self::$instance = new BB_Addon_Toolbox();
        }

        return self::$instance;
    }

    /**
     * Include excerpt.
     *
     * @since 1.0.0
     * @return void
     */
    public function load_toolbox()
    {
        if (class_exists('FLBuilder')) {
            add_filter('fl_builder_ui_bar_buttons', array(
                $this,
                'filter_builder_ui_bar_buttons'
            ));

            FLBuilderAJAX::add_action('save_toolbox_settings', 'BB_Addon_Toolbox::save_toolbox_settings', array(
                'settings',
                'post_id'
            ));

            add_filter('fl_builder_ui_js_config', array(
                $this,
                'toolbox_builder_ui_js_config'
            ));

            require_once 'settings/bb-toolbox-form.php';
        }
    }

    /**
     * Include row and column setting extendor.
     *
     * @since 1.0.0
     * @return void
     */
    public function loader()
    {
        if (class_exists('FLBuilder')) {

            $this->load_textdomain();
        }
    }

    /**
     * Load language files.
     *
     * @since 1.0.0
     * @return void
     */
    public function load_textdomain()
    {
        load_plugin_textdomain('bb-toolbox', FALSE, basename(dirname(__FILE__)) . '/languages/');
    }

    /**
     * Load in excerpt JS.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function load_scripts()
    {
        if (class_exists('FLBuilderModel') && FLBuilderModel::is_builder_active()) {
            wp_enqueue_script('bb-toolbox', BB_Toolbox_URL . 'assets/js/bb-toolbox.js', array('jquery'), rand(), TRUE);
            wp_enqueue_style('bb-toolbox', BB_Toolbox_URL . 'assets/css/bb-toolbox.css');
        }
    }

    /**
     * Admin notices.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function admin_notices()
    {
        if (!is_admin()) {
            return;
        } else {
            if (!is_user_logged_in()) {
                return;
            } else {
                if (!current_user_can('update_core')) {
                    return;
                }
            }
        }

        if (!is_plugin_active('bb-plugin/fl-builder.php')) {
            if (!is_plugin_active('beaver-builder-lite-version/fl-builder.php')) {
                echo sprintf('<div class="notice notice-error"><p>%s</p></div>', __('Please install and activate <a href="https://wordpress.org/plugins/beaver-builder-lite-version/" target="_blank">Beaver Builder Lite</a> or <a href="https://www.wpbeaverbuilder.com/pricing/" target="_blank">Beaver Builder Pro / Agency</a> to use excerpt Editor for Beaver Builder.', 'bb-toolbox'));
            }
        }
    }

    /**
     * Add Excerpt to the config
     * @param $array
     * @return mixed
     */
    public function toolbox_builder_ui_js_config($array)
    {
        $post = get_post(get_the_ID());

        $hasSeoPlugin = self::has_seo_plugin(self::detect_seo_plugins());

        $array['toolboxSettings']['bb-toolbox-type-seo'] = $hasSeoPlugin ? '1' : '0';
        if ($hasSeoPlugin) {
            $meta = self::get_seo_plugin($post);

            $array['toolboxSettings']['meta_title'] = $meta[0];
            $array['toolboxSettings']['meta_description'] = $meta[1];
        }

        $array['toolboxSettings']['post_title'] = get_the_title();
        $array['toolboxSettings']['post_permalink'] = $post->post_name;

        if ('post' !== $post->post_type) {
            require_once(ABSPATH . 'wp-admin/includes/template.php');
            require_once(ABSPATH . 'wp-admin/includes/theme.php');
            $template = !empty($post->page_template) ? $post->page_template : 'default';
            $array['toolboxSettings']['post_template'] = (string)$template;
            if (0 != count(get_page_templates())) {
                $postTemplates = array();
                $templates = get_page_templates(NULL, $post->post_type);
                ksort($templates);
                foreach (array_keys($templates) as $template) {
                    $postTemplates[esc_attr($templates[$template])] = esc_html($template);
                }
                $array['toolboxTabs']['general']['sections']['general']['fields']['post_template']['options'] = $postTemplates;
                $array['toolboxSettings']['bb-toolbox-has-templates'] = '1';
            } else {
                $array['toolboxSettings']['bb-toolbox-has-templates'] = '0'; // We don't need the template default then.
            }

            if (is_post_type_hierarchical($post->post_type)) {
                $dropdown_args = array(
                    'post_type' => $post->post_type,
                    'exclude_tree' => $post->ID,
                    'sort_column' => 'menu_order, post_title'
                );

                $dropdown_args = apply_filters('page_attributes_dropdown_pages_args', $dropdown_args, $post);

                $pages = self::get_dropdown_pages($dropdown_args);
                if (0 === count($pages)) {
                    $array['toolboxSettings']['bb-toolbox-can-parent'] = '0';
                } else {
                    $array['toolboxSettings']['bb-toolbox-can-parent'] = '1';
                    $array['toolboxSettings']['post_parent'] = ($post->post_parent === 0 ? 'default' : (string)$post->post_parent);
                    $array['toolboxTabs']['general']['sections']['general']['fields']['post_parent']['options'] = $pages;
                }
            } else {
                $array['toolboxSettings']['bb-toolbox-can-parent'] = '0';
            }
        } else {
            $array['toolboxSettings']['bb-toolbox-has-templates'] = '0';
            $array['toolboxSettings']['bb-toolbox-can-parent'] = '0';
        }


        if (get_theme_support('post-thumbnails')) {
            $array['toolboxSettings']['bb-toolbox-has-post-thumbnails'] = '1';
            if ($value = get_post_thumbnail_id()) {
                $array['toolboxSettings']['post_featuredImage'] = $value;
                $array['toolboxSettings']['post_featuredImage_src'] = get_the_post_thumbnail_url();
            } else {
                $array['toolboxSettings']['post_featuredImage'] = '';
            }
        } else {
            $array['toolboxSettings']['bb-toolbox-has-post-thumbnails'] = '0';
            $array['toolboxSettings']['post_featuredImage'] = '';
        }

        return $array;
    }

    /**
     * New Ajax method to save the excerpt Settings for the post / page
     *
     * @since 1.0.0
     * @param array $settings
     * @param string $post_id
     * @return array
     */
    static public function save_toolbox_settings($settings = array(), $post_id = '')
    {
        $post = get_post($post_id);
        $newLink = FALSE;

        $postarr = array(
            'ID' => $post_id,
        );

        if ($settings['bb-toolbox-has-post-thumbnails'] === "1") {

            $featuredImage = $settings['post_featuredImage'];
            if (empty ($featuredImage)) {
                delete_post_meta($post_id, '_thumbnail_id');
            } else {
                set_post_thumbnail($post_id, $featuredImage);
            }
        }

        if ($settings['post_title'] !== $post->post_title) {
            $postarr['post_title'] = $settings['post_title'];
        }

        if ($settings['post_permalink'] !== $post->post_name) {
            $postarr['post_name'] = $settings['post_permalink'];
            $newLink = TRUE;
        }

        if (is_post_type_hierarchical($post->post_type)) {


            // Als post_parent Default is; En parent is 0; Ignore;
            if (
                ($settings['post_parent'] !== 'default' && $post->post_parent === 0)
                ||
                ($settings['post_parent'] === 'default' && $post->post_parent !== 0)
                ||
                ($settings['post_parent'] !== 'default' && intval($settings['post_parent']) !== $post->post_parent)
            ) {
                $postarr['post_parent'] = $settings['post_parent'] === 'default' ? 0 : intval($settings['post_parent']);
                $newLink = TRUE;
            }

        }

        if ($settings['bb-toolbox-has-templates'] === 1 && ($settings['post_template'] !== $post->page_template)) {
            $postarr['post_template'] = $settings['post_template'];
        }

        $hasSeoPlugin = self::has_seo_plugin(self::detect_seo_plugins());
        if ($hasSeoPlugin) {
            $meta = self::get_seo_plugin($post);

            if ($meta[0] !== $settings['meta_title']) {
                update_post_meta($post_id, $meta[2], $settings['meta_title']);
            }

            if ($meta[1] !== $settings['meta_description']) {
                update_post_meta($post_id, $meta[3], $settings['meta_description']);
            }
        }

        wp_update_post($postarr);

        return self::answer_after_update($post_id, $newLink);
    }

    /**
     * @param $post_id
     * @param bool $newPermaLink
     * @return array
     */
    static public function answer_after_update($post_id, $newPermaLink = FALSE)
    {
        $post = get_post($post_id);

        $result = array();
        $result['newlink'] = $newPermaLink ? TRUE : FALSE;


        $hasSeoPlugin = self::has_seo_plugin(self::detect_seo_plugins());
        $result['bb-toolbox-type-seo'] = $hasSeoPlugin ? '1' : '0';
        if ($hasSeoPlugin) {
            $meta = self::get_seo_plugin($post);

            $result['meta_title'] = $meta[0];
            $result['meta_description'] = $meta[1];
        }

        $result['post_title'] = $post->post_title;
        $result['post_permalink'] = $post->post_name;

        if ('post' !== $post->post_type) {
            require_once(ABSPATH . 'wp-admin/includes/template.php');
            require_once(ABSPATH . 'wp-admin/includes/theme.php');
            $template = !empty($post->page_template) ? $post->page_template : 'default';
            $result['post_template'] = (string)$template;
            if (0 != count(get_page_templates())) {
                $result['bb-toolbox-has-templates'] = '1';
            } else {
                $result['bb-toolbox-has-templates'] = '0'; // We don't need the template default then.
            }

            if (is_post_type_hierarchical($post->post_type)) {
                $dropdown_args = array(
                    'post_type' => $post->post_type,
                    'exclude_tree' => $post->ID,
                    'sort_column' => 'menu_order, post_title'
                );

                $dropdown_args = apply_filters('page_attributes_dropdown_pages_args', $dropdown_args, $post);
                $pages = self::get_dropdown_pages($dropdown_args);
                if (0 === count($pages)) {
                    $result['bb-toolbox-can-parent'] = '0';
                } else {
                    $result['bb-toolbox-can-parent'] = '1';
                    $result['post_parent'] = ($post->post_parent === 0 ? 'default' : (string)$post->post_parent);
                }
            } else {
                $result['bb-toolbox-can-parent'] = '0';
            }
        } else {
            $result['bb-toolbox-has-templates'] = '0';
            $result['bb-toolbox-can-parent'] = '0';
        }

        if (get_theme_support('post-thumbnails')) {
            $result['bb-toolbox-has-post-thumbnails'] = '1';
            if ($value = get_post_thumbnail_id()) {
                $result['post_featuredImage'] = $value;
                $result['post_featuredImage_src'] = get_the_post_thumbnail_url();
            } else {
                $result['post_featuredImage'] = '';
            }
        } else {
            $result['bb-toolbox-has-post-thumbnails'] = '0';
            $result['post_featuredImage'] = '';
        }

        return $result;
    }

    /**
     * Adds button to the bar
     * @param $buttons
     * @return mixed
     */
    public function filter_builder_ui_bar_buttons($buttons)
    {
        $buttons['toolbox'] = array(
            'label' => __('Page / Post Config', 'bb-toolbox')
        );
        return $buttons;
    }
}

// Load the PowerPack class.
$bb_addon_toolbox = BB_Addon_Toolbox::get_instance();
