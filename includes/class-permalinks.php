<?php
class CUNY_CPT_Permalinks {

    public function __construct() {
        add_filter('post_type_link', array($this, 'cpt_post_type_link'), 10, 4);
    }

    public function cpt_post_type_link($post_link, $post, $leavename, $sample) {
        $cpt = get_post_type($post);
        $cpts = array_keys(get_option('cuny_dynamic_cpts', []));
        if (in_array($cpt, $cpts) && false !== strpos($post_link, '%office%')) {
            $office_terms = wp_get_object_terms($post->ID, 'office');
            if (!empty($office_terms)) {
                $post_link = str_replace('%office%', $office_terms[0]->slug, $post_link);
            } else {
                $post_link = str_replace('%office%/', '', $post_link);
            }
        }
        return $post_link;
    }
}