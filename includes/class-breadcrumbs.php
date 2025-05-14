<?php
class CUNY_Letters_Breadcrumbs {

    public function __construct() {
        add_action('cuny_letters_breadcrumbs', array($this, 'display_breadcrumbs'));
        add_filter('the_content', array($this, 'prepend_breadcrumbs_to_content'));
    }

    public function display_breadcrumbs() {
        if (is_singular('letters')) {
            global $post;
            $office_terms = wp_get_object_terms($post->ID, 'office');
            $office_name = !empty($office_terms) ? $office_terms[0]->name : '';
            $office_link = !empty($office_terms) ? get_term_link($office_terms[0]) : '';

            echo '<nav class="breadcrumb" aria-label="Breadcrumb">';
            echo '<a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'cuny-letters-cpt') . '</a>';
            echo ' &raquo; <a href="' . esc_url(get_post_type_archive_link('letters')) . '">' . esc_html__('Letters', 'cuny-letters-cpt') . '</a>';
            if ($office_name && !is_wp_error($office_link)) {
                echo ' &raquo; <a href="' . esc_url($office_link) . '">' . esc_html($office_name) . '</a>';
            }
            echo ' &raquo; <span aria-current="page">' . esc_html(get_the_title()) . '</span>';
            echo '</nav>';
        }
    }

    public function prepend_breadcrumbs_to_content($content) {
        if (is_singular('letters')) {
            ob_start();
            $this->display_breadcrumbs();
            $breadcrumbs = ob_get_clean();
            return $breadcrumbs . $content;
        }
        return $content;
    }
}