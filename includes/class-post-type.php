<?php
class CUNY_Letters_Post_Type {

    public function register() {
        $labels = array(
            'name'                  => _x('Letters', 'Post Type General Name', 'cuny-letters-cpt'),
            'singular_name'         => _x('Letter', 'Post Type Singular Name', 'cuny-letters-cpt'),
            'menu_name'             => __('Letters', 'cuny-letters-cpt'),
            'name_admin_bar'        => __('Letter', 'cuny-letters-cpt'),
            'archives'              => __('Letter Archives', 'cuny-letters-cpt'),
            'attributes'            => __('Letter Attributes', 'cuny-letters-cpt'),
            'parent_item_colon'     => __('Parent Letter:', 'cuny-letters-cpt'),
            'all_items'             => __('All Letters', 'cuny-letters-cpt'),
            'add_new_item'          => __('Add New Letter', 'cuny-letters-cpt'),
            'add_new'               => __('Add New', 'cuny-letters-cpt'),
            'new_item'              => __('New Letter', 'cuny-letters-cpt'),
            'edit_item'             => __('Edit Letter', 'cuny-letters-cpt'),
            'update_item'           => __('Update Letter', 'cuny-letters-cpt'),
            'view_item'             => __('View Letter', 'cuny-letters-cpt'),
            'view_items'            => __('View Letters', 'cuny-letters-cpt'),
            'search_items'          => __('Search Letter', 'cuny-letters-cpt'),
            'not_found'             => __('Not found', 'cuny-letters-cpt'),
            'not_found_in_trash'    => __('Not found in Trash', 'cuny-letters-cpt'),
            'featured_image'        => __('Featured Image', 'cuny-letters-cpt'),
            'set_featured_image'    => __('Set featured image', 'cuny-letters-cpt'),
            'remove_featured_image' => __('Remove featured image', 'cuny-letters-cpt'),
            'use_featured_image'    => __('Use as featured image', 'cuny-letters-cpt'),
            'insert_into_item'      => __('Insert into letter', 'cuny-letters-cpt'),
            'uploaded_to_this_item' => __('Uploaded to this letter', 'cuny-letters-cpt'),
            'items_list'            => __('Letters list', 'cuny-letters-cpt'),
            'items_list_navigation' => __('Letters list navigation', 'cuny-letters-cpt'),
            'filter_items_list'     => __('Filter letters list', 'cuny-letters-cpt'),
        );

        $args = array(
            'label'                 => __('Letter', 'cuny-letters-cpt'),
            'description'           => __('Official letters organized by office', 'cuny-letters-cpt'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions'),
            'taxonomies'            => array('office'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-email-alt',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rewrite'               => array(
                'slug' => 'letters',
                'with_front' => false
            ),
        );

        register_post_type('letters', $args);
    }
}