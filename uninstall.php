<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Clean up options
delete_option('cuny_letters_cpt_version');

// Clean up custom post type data
$letters = get_posts(array(
    'post_type' => 'letters',
    'numberposts' => -1,
    'post_status' => 'any'
));

foreach ($letters as $letter) {
    wp_delete_post($letter->ID, true);
}

// Clean up taxonomy terms
$terms = get_terms(array(
    'taxonomy' => 'office',
    'hide_empty' => false
));

foreach ($terms as $term) {
    wp_delete_term($term->term_id, 'office');
}

// Flush rewrite rules
flush_rewrite_rules();