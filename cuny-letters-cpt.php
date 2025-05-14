<?php
/**
 * Plugin Name: CUNY Letters CPT
 * Plugin URI: https://github.com/millaw/cuny-letters-cpt
 * Description: Custom post type for managing letters organized by office
 * Version: 1.0.0
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