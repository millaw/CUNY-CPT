<?php
class CUNY_CPT_Taxonomy {

    public function register() {
        $labels = array(
            'name'                       => _x('Offices/Initiatives', 'Taxonomy General Name', 'cuny-cpt'),
            'singular_name'              => _x('Office/Initiative', 'Taxonomy Singular Name', 'cuny-cpt'),
            'search_items'               => __('Search Offices/Initiatives', 'cuny-cpt'),
            'popular_items'              => __('Popular Offices/Initiatives', 'cuny-cpt'),
            'all_items'                  => __('All Offices/Initiatives', 'cuny-cpt'),
            'parent_item'                => __('Parent Office/Initiative', 'cuny-cpt'),
            'parent_item_colon'          => __('Parent Office/Initiative:', 'cuny-cpt'),
            'edit_item'                  => __('Edit Office/Initiative', 'cuny-cpt'),
            'view_item'                  => __('View Office/Initiative', 'cuny-cpt'),
            'update_item'                => __('Update Office/Initiative', 'cuny-cpt'),
            'add_new_item'               => __('Add New Office/Initiative', 'cuny-cpt'),
            'new_item_name'              => __('New Office/Initiative Name', 'cuny-cpt'),
            'separate_items_with_commas' => __('Separate offices/initiatives with commas', 'cuny-cpt'),
            'add_or_remove_items'        => __('Add or remove offices/initiatives', 'cuny-cpt'),
            'choose_from_most_used'      => __('Choose from the most used', 'cuny-cpt'),
            'not_found'                  => __('No offices/initiatives found', 'cuny-cpt'),
            'no_terms'                   => __('No offices/initiatives', 'cuny-cpt'),
            'items_list'                 => __('Offices/Initiatives list', 'cuny-cpt'),
            'items_list_navigation'      => __('Offices/Initiatives list navigation', 'cuny-cpt'),
            'menu_name'                  => __('Offices/Initiatives', 'cuny-cpt'),
            'name_admin_bar'             => __('Office/Initiative', 'cuny-cpt'),
            'add_new'                    => __('Add New Office/Initiative', 'cuny-cpt'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => false,
            'show_in_rest'               => true,
            'meta_box_cb'                => null, // Use default metabox for hierarchical taxonomy (checkboxes, not category dropdown)
            'rewrite'                    => array(
                'slug' => 'office',
                'with_front' => false,
                'hierarchical' => true,
            ),
        );

        // Attach Office taxonomy to all CPTs (dynamic, and also support CPT UI if present)
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        $post_types = array();
        if (is_plugin_active('custom-post-type-ui/custom-post-type-ui.php')) {
            $cptui_types = get_option('cptui_post_types', []);
            $post_types = array_keys($cptui_types);
        } else {
            $cpts = get_option('cuny_dynamic_cpts', array());
            foreach ($cpts as $slug => $labels) {
                $post_types[] = $slug;
            }
        }
        if (!empty($post_types)) {
            register_taxonomy('office', $post_types, $args);
        }
    }
}
