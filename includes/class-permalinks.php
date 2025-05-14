<?php
class CUNY_Letters_Permalinks {

    public function __construct() {
        add_filter('post_type_link', array($this, 'letters_post_type_link'), 10, 4);
    }

    public function letters_post_type_link($post_link, $post, $leavename, $sample) {
        if (false !== strpos($post_link, '%office%')) {
            $office_terms = wp_get_object_terms($post->ID, 'office');
            if (!empty($office_terms)) {
                $post_link = str_replace('%office%', $office_terms[0]->slug, $post_link);
            } else {
                // Fallback if no office is assigned
                $post_link = str_replace('%office%/', '', $post_link);
            }
        }
        return $post_link;
    }
}