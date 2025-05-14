<?php
class CUNY_Letters_Taxonomy {

    public function register() {
        $labels = array(
            'name'                       => _x('Offices', 'Taxonomy General Name', 'cuny-letters-cpt'),
            'singular_name'              => _x('Office', 'Taxonomy Singular Name', 'cuny-letters-cpt'),
            'menu_name'                  => __('Offices', 'cuny-letters-cpt'),
            'all_items'                  => __('All Offices', 'cuny-letters-cpt'),
            'parent_item'                => __('Parent Office', 'cuny-letters-cpt'),
            'parent_item_colon'          => __('Parent Office:', 'cuny-letters-cpt'),
            'new_item_name'              => __('New Office Name', 'cuny-letters-cpt'),
            'add_new_item'               => __('Add New Office', 'cuny-letters-cpt'),
            'edit_item'                  => __('Edit Office', 'cuny-letters-cpt'),
            'update_item'                => __('Update Office', 'cuny-letters-cpt'),
            'view_item'                  => __('View Office', 'cuny-letters-cpt'),
            'separate_items_with_commas' => __('Separate offices with commas', 'cuny-letters-cpt'),
            'add_or_remove_items'        => __('Add or remove offices', 'cuny-letters-cpt'),
            'choose_from_most_used'      => __('Choose from the most used', 'cuny-letters-cpt'),
            'popular_items'              => __('Popular Offices', 'cuny-letters-cpt'),
            'search_items'               => __('Search Offices', 'cuny-letters-cpt'),
            'not_found'                  => __('Not Found', 'cuny-letters-cpt'),
            'no_terms'                   => __('No offices', 'cuny-letters-cpt'),
            'items_list'                 => __('Offices list', 'cuny-letters-cpt'),
            'items_list_navigation'      => __('Offices list navigation', 'cuny-letters-cpt'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => false,
            'show_in_rest'               => true,
            'rewrite'                    => array(
                'slug' => 'office',
                'with_front' => false,
                'hierarchical' => true,
            ),
        );

        register_taxonomy('office', array('letters'), $args);
    }
}
