<?php
/**
 * Plugin Name: CUNY CPT & Office Extension
 * Plugin URI: https://github.com/millaw/cuny-cpt
 * Description: Adds an "Office" taxonomy to custom post types. Works as an extension to CPT UI if installed, or as a standalone CPT manager.
 * Version: 2.3.1
 * Author: Milla Wynn
 * Author URI: https://github.com/millaw
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: cuny-cpt
 */

defined('ABSPATH') or die('Direct access not allowed');

define('CUNY_CPT_VERSION', '2.3.1');
define('CUNY_CPT_PATH', plugin_dir_path(__FILE__));
define('CUNY_CPT_URL', plugin_dir_url(__FILE__));

require_once CUNY_CPT_PATH . 'includes/class-breadcrumbs.php';
require_once CUNY_CPT_PATH . 'includes/class-permalinks.php';
require_once CUNY_CPT_PATH . 'includes/class-post-type.php';
require_once CUNY_CPT_PATH . 'includes/class-taxonomy.php';

add_action('init', function() {
    $taxonomy = new CUNY_CPT_Taxonomy();
    $taxonomy->register();
}, 0);

// Force taxonomy archive title to always show the term name for Office/Initiative
add_filter('get_the_archive_title', function($title) {
    if (is_tax('office')) {
        $term = get_queried_object();
        if ($term && !is_wp_error($term)) {
            $title = esc_html($term->name);
            // Remove prefix like "Archives: " if present
        }
    }
    return $title;
}, 20);

// Inject Office/Initiative archive title into empty <h1 class="page-title"> if needed
add_action('wp_footer', function() {
    if (is_tax('office')) {
        $term = get_queried_object();
        if ($term && !is_wp_error($term)) {
            $title = esc_html($term->name);
            $cpt_label = '';
            // Try to get the CPT from the first post in the loop (most accurate for taxonomy archives)
            global $wp_query;
            if (!empty($wp_query->posts) && isset($wp_query->posts[0])) {
                $first_post = $wp_query->posts[0];
                $post_type = get_post_type($first_post);
                if ($post_type) {
                    $cpt_obj = get_post_type_object($post_type);
                    $cpt_label = $cpt_obj && isset($cpt_obj->labels->name) ? $cpt_obj->labels->name : '';
                }
            }
            // Fallback: get first CPT registered to this taxonomy
            if (!$cpt_label) {
                $tax = get_taxonomy('office');
                if ($tax && !empty($tax->object_type)) {
                    $cpt_obj = get_post_type_object($tax->object_type[0]);
                    $cpt_label = $cpt_obj && isset($cpt_obj->labels->name) ? $cpt_obj->labels->name : '';
                }
            }
            ?>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var h1 = document.querySelector('h1.page-title');
                if (h1 && h1.textContent.trim() === '') {
                    h1.textContent = <?php echo json_encode(trim($cpt_label ? $cpt_label . ': ' . $title : $title)); ?>;
                }
            });
            </script>
            <?php
        }
    }
});

class CUNY_CPT_Office_Extension {
    private $is_cptui;
    private $office_taxonomy = 'office';

    public function __construct() {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        $this->is_cptui = is_plugin_active('custom-post-type-ui/custom-post-type-ui.php');

        add_action('init', [$this, 'register_dynamic_cpts']);
        add_action('init', [$this, 'register_office_taxonomy']);
        add_action('admin_menu', [$this, 'admin_menu']);
        new CUNY_CPT_Breadcrumbs($this->office_taxonomy);
        new CUNY_CPT_Permalinks();
    }

    public function register_dynamic_cpts() {
        if ($this->is_cptui) return; // Let CPT UI handle CPTs if present
        $cpts = get_option('cuny_dynamic_cpts', []);
        foreach ($cpts as $slug => $labels) {
            $args = [
                'label' => $labels['plural'],
                'labels' => [
                    'name' => $labels['plural'],
                    'singular_name' => $labels['singular'],
                    'menu_name' => $labels['plural'],
                    'add_new' => __('Add New', 'cuny-cpt'),
                    'add_new_item' => sprintf(__('Add New %s', 'cuny-cpt'), $labels['singular']),
                    'edit_item' => sprintf(__('Edit %s', 'cuny-cpt'), $labels['singular']),
                    'new_item' => sprintf(__('New %s', 'cuny-cpt'), $labels['singular']),
                    'view_item' => sprintf(__('View %s', 'cuny-cpt'), $labels['singular']),
                    'view_items' => sprintf(__('View %s', 'cuny-cpt'), $labels['plural']),
                    'search_items' => sprintf(__('Search %s', 'cuny-cpt'), $labels['plural']),
                    'not_found' => __('Not found', 'cuny-cpt'),
                    'not_found_in_trash' => __('Not found in Trash', 'cuny-cpt'),
                    'all_items' => sprintf(__('All %s', 'cuny-cpt'), $labels['plural']),
                    'archives' => sprintf(__('%s Archives', 'cuny-cpt'), $labels['singular']),
                ],
                'public' => true,
                'has_archive' => true,
                'rewrite' => [
                    'slug' => $slug,
                    'with_front' => false
                ],
                'show_in_menu' => true,
                'supports' => ['title', 'editor', 'thumbnail'],
                'taxonomies' => [$this->office_taxonomy],
            ];
            register_post_type($slug, $args);
        }
    }

    public function register_office_taxonomy() {
        $cpts = $this->get_target_cpts();
        register_taxonomy($this->office_taxonomy, $cpts, [
            'label' => __('Office/Initiative', 'cuny-cpt'),
            'hierarchical' => true,
            'public' => true,
            'show_admin_column' => true,
            'rewrite' => ['slug' => 'office'],
        ]);
    }

    private function get_target_cpts() {
        if ($this->is_cptui) {
            $cptui_types = get_option('cptui_post_types', []);
            return array_keys($cptui_types);
        } else {
            $cpts = get_option('cuny_dynamic_cpts', []);
            return empty($cpts) ? [] : array_keys($cpts);
        }
    }

    public function admin_menu() {
        if ($this->is_cptui) {
            add_menu_page(
                __('Office Taxonomy', 'cuny-cpt'),
                __('Office Taxonomy', 'cuny-cpt'),
                'manage_options',
                'cuny-office-manager',
                [$this, 'office_admin_page'],
                'dashicons-networking',
                25
            );
        } else {
            add_menu_page(
                __('CUNY CPT Manager', 'cuny-cpt'),
                __('CUNY CPT Manager', 'cuny-cpt'),
                'manage_options',
                'cuny-cpt-manager',
                [$this, 'standalone_admin_page'],
                'dashicons-admin-post',
                25
            );
        }
    }

    public function office_admin_page() {
        echo '<div class="wrap"><h1>' . esc_html__('Manage Office Taxonomy', 'cuny-cpt') . '</h1>';
        echo '<p>' . esc_html__('Use the standard WordPress taxonomy UI to add/edit Offices. You can assign Offices to any CPT registered via CPT UI.', 'cuny-cpt') . '</p>';
        echo '<a href="' . esc_url(admin_url('edit-tags.php?taxonomy=office')) . '" class="button button-primary">' . esc_html__('Manage Offices', 'cuny-cpt') . '</a>';
        echo '</div>';
    }

    public function standalone_admin_page() {
        echo '<div class="wrap"><h1>' . esc_html__('Create a Custom Post Type and Office', 'cuny-cpt') . '</h1>';
        if (!empty($_POST['cpt_slug']) && !empty($_POST['cpt_singular']) && !empty($_POST['cpt_plural'])) {
            $slug = sanitize_key($_POST['cpt_slug']);
            $singular = sanitize_text_field($_POST['cpt_singular']);
            $plural = sanitize_text_field($_POST['cpt_plural']);
            $cpts = get_option('cuny_dynamic_cpts', []);
            $cpts[$slug] = ['singular' => $singular, 'plural' => $plural];
            update_option('cuny_dynamic_cpts', $cpts);
            echo '<div class="updated notice"><p>' . esc_html__('Custom Post Type created! Please reload the page.', 'cuny-cpt') . '</p></div>';
        }
        echo '<form method="post">';
        echo '<table class="form-table">';
        echo '<tr><th><label for="cpt_slug">' . esc_html__('Slug', 'cuny-cpt') . '</label></th><td><input name="cpt_slug" id="cpt_slug" type="text" required></td></tr>';
        echo '<tr><th><label for="cpt_singular">' . esc_html__('Singular Name', 'cuny-cpt') . '</label></th><td><input name="cpt_singular" id="cpt_singular" type="text" required></td></tr>';
        echo '<tr><th><label for="cpt_plural">' . esc_html__('Plural Name', 'cuny-cpt') . '</label></th><td><input name="cpt_plural" id="cpt_plural" type="text" required></td></tr>';
        echo '</table>';
        submit_button(__('Create Custom Post Type', 'cuny-cpt'));
        echo '</form>';
        echo '<hr><a href="' . esc_url(admin_url('edit-tags.php?taxonomy=office')) . '" class="button">' . esc_html__('Manage Offices', 'cuny-cpt') . '</a>';
        echo '</div>';
    }
}

// Register dynamic CPTs if standalone
add_action('init', function() {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    $is_cptui = is_plugin_active('custom-post-type-ui/custom-post-type-ui.php');
    if (!$is_cptui) {
        $cpts = get_option('cuny_dynamic_cpts', []);
        foreach ($cpts as $slug => $labels) {
            register_post_type($slug, [
                'label' => $labels['plural'],
                'labels' => [
                    'name' => $labels['plural'],
                    'singular_name' => $labels['singular'],
                ],
                'public' => true,
                'has_archive' => true,
                'show_in_menu' => true,
                'supports' => ['title', 'editor', 'thumbnail'],
                'rewrite' => [
                    'slug' => $slug,
                    'with_front' => false
                ],
            ]);
        }
    }
});

register_activation_hook(__FILE__, function() {
    flush_rewrite_rules();
});
register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});

new CUNY_CPT_Office_Extension();
