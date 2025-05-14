<?php
class CUNY_Letters_Admin {

    public function __construct() {
        add_action('admin_init', array($this, 'admin_init'));
    }

    public function admin_init() {
        // Add custom columns to letters list
        add_filter('manage_letters_posts_columns', array($this, 'add_custom_columns'));
        
        // Populate custom columns
        add_action('manage_letters_posts_custom_column', array($this, 'populate_custom_columns'), 10, 2);
        
        // Make office column sortable
        add_filter('manage_edit-letters_sortable_columns', array($this, 'make_columns_sortable'));
        
        // Add taxonomy filter dropdown
        add_action('restrict_manage_posts', array($this, 'add_office_filter_dropdown'));
    }

    public function add_custom_columns($columns) {
        $new_columns = array();
        
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key === 'title') {
                $new_columns['office'] = __('Office', 'cuny-letters-cpt');
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
        
        if ($typenow === 'letters') {
            $selected = isset($_GET['office']) ? $_GET['office'] : '';
            wp_dropdown_categories(array(
                'show_option_all' => __('All Offices', 'cuny-letters-cpt'),
                'taxonomy' => 'office',
                'name' => 'office',
                'value_field' => 'slug',
                'selected' => $selected,
                'hierarchical' => true,
            ));
        }
    }
}