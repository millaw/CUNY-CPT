<?php
class CUNY_CPT_Post_Type {

    public function register() {
        $labels = array(
            'name'                  => _x('Custom Posts', 'Post Type General Name', 'cuny-cpt'),
            'singular_name'         => _x('Custom Post', 'Post Type Singular Name', 'cuny-cpt'),
            'menu_name'             => __('Custom Posts', 'cuny-cpt'),
            'name_admin_bar'        => __('Custom Post', 'cuny-cpt'),
            'archives'              => __('Custom Post Archives', 'cuny-cpt'),
            'attributes'            => __('Custom Post Attributes', 'cuny-cpt'),
            'parent_item_colon'     => __('Parent Custom Post:', 'cuny-cpt'),
            'all_items'             => __('All Custom Posts', 'cuny-cpt'),
            'add_new_item'          => __('Add New Custom Post', 'cuny-cpt'),
            'add_new'               => __('Add New', 'cuny-cpt'),
            'new_item'              => __('New Custom Post', 'cuny-cpt'),
            'edit_item'             => __('Edit Custom Post', 'cuny-cpt'),
            'update_item'           => __('Update Custom Post', 'cuny-cpt'),
            'view_item'             => __('View Custom Post', 'cuny-cpt'),
            'view_items'            => __('View Custom Posts', 'cuny-cpt'),
            'search_items'          => __('Search Custom Post', 'cuny-cpt'),
            'not_found'             => __('Not found', 'cuny-cpt'),
            'not_found_in_trash'    => __('Not found in Trash', 'cuny-cpt'),
            'featured_image'        => __('Featured Image', 'cuny-cpt'),
            'set_featured_image'    => __('Set featured image', 'cuny-cpt'),
            'remove_featured_image' => __('Remove featured image', 'cuny-cpt'),
            'use_featured_image'    => __('Use as featured image', 'cuny-cpt'),
            'insert_into_item'      => __('Insert into custom post', 'cuny-cpt'),
            'uploaded_to_this_item' => __('Uploaded to this custom post', 'cuny-cpt'),
            'items_list'            => __('Custom Posts list', 'cuny-cpt'),
            'items_list_navigation' => __('Custom Posts list navigation', 'cuny-cpt'),
            'filter_items_list'     => __('Filter custom posts list', 'cuny-cpt'),
        );

        $args = array(
            'label'                 => __('Custom Post', 'cuny-cpt'),
            'description'           => __('Official custom posts organized by office', 'cuny-cpt'),
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
                'slug' => 'custom-posts',
                'with_front' => false
            ),
        );

        register_post_type('custom-posts', $args);
    }
}