<?php
/**
 * Plugin Name: CUNY Letters CPT
 * Plugin URI: https://github.com/millaw/cuny-letters-cpt
 * Description: Custom post type for managing letters organized by office
 * Version: 1.1.0
 * Author: Milla Wynn
 * Author URI: https://github.com/millaw
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: cuny-letters-cpt
 */

defined('ABSPATH') or die('Direct access not allowed');

// Define plugin constants
define('CUNY_LETTERS_CPT_VERSION', '1.0.0');
define('CUNY_LETTERS_CPT_PATH', plugin_dir_path(__FILE__));
define('CUNY_LETTERS_CPT_URL', plugin_dir_url(__FILE__));

// Include required files
require_once CUNY_LETTERS_CPT_PATH . 'includes/class-post-type.php';
require_once CUNY_LETTERS_CPT_PATH . 'includes/class-taxonomy.php';
require_once CUNY_LETTERS_CPT_PATH . 'includes/class-permalinks.php';
require_once CUNY_LETTERS_CPT_PATH . 'includes/class-breadcrumbs.php';
require_once CUNY_LETTERS_CPT_PATH . 'includes/class-admin.php';
require_once CUNY_LETTERS_CPT_PATH . 'includes/cpt-register.php';

class CUNY_Letters_CPT {

    private $post_type;
    private $taxonomy;
    private $permalinks;
    private $breadcrumbs;
    private $admin;

    public function __construct() {
        $this->post_type = new CUNY_Letters_Post_Type();
        $this->taxonomy = new CUNY_Letters_Taxonomy();
        $this->permalinks = new CUNY_Letters_Permalinks();
        $this->breadcrumbs = new CUNY_Letters_Breadcrumbs();
        $this->admin = new CUNY_Letters_Admin();

        add_action('init', array($this->post_type, 'register'));
        add_action('init', array($this->taxonomy, 'register'));

        // Register any custom post type you want here:
        function create_new_custom_post_type($type, $singular, $plural, $args = array()) {
            cuny_register_custom_post_type($type, $singular, $plural, $args);
        }

        // Add admin menu for creating new CPTs
        add_action('admin_menu', function() {
            add_menu_page(
                __('Custom Post Types', 'cuny-letters-cpt'),
                __('Custom Post Types', 'cuny-letters-cpt'),
                'manage_options',
                'cuny-cpt-manager',
                function() {
                    echo '<div class="wrap"><h1>' . esc_html__('Create a New Custom Post Type', 'cuny-letters-cpt') . '</h1>';
                    echo '<form method="post">';
                    echo '<table class="form-table">';
                    echo '<tr><th><label for="cpt_slug">' . esc_html__('Slug [ex.: custom-post-type]', 'cuny-letters-cpt') . '</label></th><td><input name="cpt_slug" id="cpt_slug" type="text" placeholder="custom-post-type" required></td></tr>';
                    echo '<tr><th><label for="cpt_singular">' . esc_html__('Singular Name [ex.: Custom Post Type]', 'cuny-letters-cpt') . '</label></th><td><input name="cpt_singular" id="cpt_singular" type="text" placeholder="Custom Post Type" required></td></tr>';
                    echo '<tr><th><label for="cpt_plural">' . esc_html__('Plural Name [ex.: Custom Post Types]', 'cuny-letters-cpt') . '</label></th><td><input name="cpt_plural" id="cpt_plural" type="text" placeholder="Custom Post Types" required></td></tr>';
                    echo '</table>';
                    submit_button(__('Create Custom Post Type', 'cuny-letters-cpt'));
                    echo '</form>';
                    if (!empty($_POST['cpt_slug']) && !empty($_POST['cpt_singular']) && !empty($_POST['cpt_plural'])) {
                        $slug = sanitize_key($_POST['cpt_slug']);
                        $singular = sanitize_text_field($_POST['cpt_singular']);
                        $plural = sanitize_text_field($_POST['cpt_plural']);
                        if (!get_option('cuny_dynamic_cpts')) {
                            add_option('cuny_dynamic_cpts', array());
                        }
                        $cpts = get_option('cuny_dynamic_cpts', array());
                        $cpts[$slug] = array('singular' => $singular, 'plural' => $plural);
                        update_option('cuny_dynamic_cpts', $cpts);
                        echo '<div class="updated notice"><p>' . esc_html__('Custom Post Type created! Please reload the page.', 'cuny-letters-cpt') . '</p></div>';
                    }
                    echo '</div>';
                },
                'dashicons-admin-post',
                25
            );
        });

        // Register dynamic CPTs from options
        add_action('init', function() {
            $cpts = get_option('cuny_dynamic_cpts', array());
            foreach ($cpts as $slug => $labels) {
                create_new_custom_post_type($slug, $labels['singular'], $labels['plural']);
            }
        });

        add_action('init', function() {
            create_new_custom_post_type('letters', 'Letter', 'Letters');
            // To add another CPT, just call:
            // create_new_custom_post_type('events', 'Event', 'Events');
            // create_new_custom_post_type('news', 'News', 'News');
        });

        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    public function activate() {
        $this->post_type->register();
        $this->taxonomy->register();
        flush_rewrite_rules();
    }

    public function deactivate() {
        flush_rewrite_rules();
    }
}

new CUNY_Letters_CPT();
