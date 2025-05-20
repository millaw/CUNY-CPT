<?php
class CUNY_CPT_Admin {

    public function __construct() {
        add_action('admin_init', array($this, 'admin_init'));
    }

    public function admin_init() {
        // Add custom columns to the student-stories CPT (or all CPTs if CPT UI is present)
        $cpts = $this->get_target_cpts();
        foreach ($cpts as $cpt) {
            add_filter("manage_{$cpt}_posts_columns", array($this, 'add_custom_columns'));
            add_action("manage_{$cpt}_posts_custom_column", array($this, 'populate_custom_columns'), 10, 2);
            add_filter("manage_edit-{$cpt}_sortable_columns", array($this, 'make_columns_sortable'));
        }
        add_action('restrict_manage_posts', array($this, 'add_office_filter_dropdown'));
    }

    private function get_target_cpts() {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        if (is_plugin_active('custom-post-type-ui/custom-post-type-ui.php')) {
            $cptui_types = get_option('cptui_post_types', []);
            return array_keys($cptui_types);
        } else {
            $cpts = get_option('cuny_dynamic_cpts', []);
            return empty($cpts) ? [] : array_keys($cpts);
        }
    }

    public function add_custom_columns($columns) {
        $new_columns = array();
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key === 'title') {
                $new_columns['office'] = __('Office', 'cuny-cpt');
            }
        }
        return $new_columns;
    }

    public function populate_custom_columns($column, $post_id) {
        if ($column === 'office') {
            $terms = get_the_terms($post_id, 'office');
            if ($terms && !is_wp_error($terms)) {
                $office_names = array();
                foreach ($terms as $term) {
                    $office_names[] = $term->name;
                }
                echo esc_html(implode(', ', $office_names));
            }
        }
    }

    public function make_columns_sortable($columns) {
        $columns['office'] = 'office';
        return $columns;
    }

    public function add_office_filter_dropdown() {
        global $typenow;
        $cpts = $this->get_target_cpts();
        if (in_array($typenow, $cpts)) {
            $selected = isset($_GET['office']) ? $_GET['office'] : '';
            wp_dropdown_categories(array(
                'show_option_all' => __('All Offices', 'cuny-cpt'),
                'taxonomy' => 'office',
                'name' => 'office',
                'value_field' => 'slug',
                'selected' => $selected,
                'hierarchical' => true,
            ));
        }
    }
}