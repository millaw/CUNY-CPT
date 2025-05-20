<?php
/**
 * Generic Custom Post Type registration for CUNY CPT Plugin
 */
if (!function_exists('cuny_register_custom_post_type')) {
    function cuny_register_custom_post_type($type, $singular, $plural, $args = array()) {
        $labels = array(
            'name' => $plural,
            'singular_name' => $singular,
            'menu_name' => $plural,
            'name_admin_bar' => $singular,
            'add_new' => __('Add New', 'cuny-cpt'),
            'add_new_item' => sprintf(__('Add New %s', 'cuny-cpt'), $singular),
            'edit_item' => sprintf(__('Edit %s', 'cuny-cpt'), $singular),
            'new_item' => sprintf(__('New %s', 'cuny-cpt'), $singular),
            'view_item' => sprintf(__('View %s', 'cuny-cpt'), $singular),
            'view_items' => sprintf(__('View %s', 'cuny-cpt'), $plural),
            'search_items' => sprintf(__('Search %s', 'cuny-cpt'), $plural),
            'not_found' => __('Not found', 'cuny-cpt'),
            'not_found_in_trash' => __('Not found in Trash', 'cuny-cpt'),
            'all_items' => sprintf(__('All %s', 'cuny-cpt'), $plural),
            'archives' => sprintf(__('%s Archives', 'cuny-cpt'), $singular),
        );
        $defaults = array(
            'label' => $singular,
            'labels' => $labels,
            'public' => true,
            'show_ui' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => strtolower($type)),
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions'),
            'show_in_rest' => true,
        );
        $args = array_merge($defaults, $args);
        register_post_type($type, $args);
    }
}
