<?php
class CUNY_CPT_Breadcrumbs {
    private $office_taxonomy;

    public function __construct($office_taxonomy = 'office') {
        $this->office_taxonomy = $office_taxonomy;
        add_filter('the_content', array($this, 'remove_breadcrumbs_from_content'));
        add_action('wp_footer', array($this, 'replace_theme_breadcrumbs_div'));
    }

    // Remove breadcrumbs from content (never inject into <main> or anywhere else)
    public function remove_breadcrumbs_from_content($content) {
        return $content;
    }

    // Output breadcrumbs only in #breadcrumbs ul.inline, preserving theme's original structure
    public function replace_theme_breadcrumbs_div() {
        if (is_singular()) {
            global $post;
            $cpt = get_post_type($post);
            $tax = $this->office_taxonomy;
            $terms = get_the_terms($post, $tax);
            if (!$terms || is_wp_error($terms)) return;
            $office = $terms[0];
            $office_link = get_term_link($office);
            if (is_wp_error($office_link)) return;
            $home_url = esc_url(home_url('/'));
            $cpt_obj = get_post_type_object($cpt);
            $cpt_label = $cpt_obj ? $cpt_obj->labels->name : ucfirst($cpt);
            $cpt_archive = get_post_type_archive_link($cpt);
            $title = get_the_title($post);
            // Build <li> breadcrumbs array
            $crumbs = array();
            // Home Page /
            $crumbs[] = '<li><a href="' . $home_url . '">' . esc_html__('Home', 'cuny-cpt') . '</a></li>';
            if ($cpt_archive) {
                // Go to CPT Archive page / 
                $crumbs[] = '<li><a href="' . esc_url($cpt_archive) . '">' . esc_html($cpt_label) . '</a></li>';
            } else {
                // Go to CPT Office Page
                $crumbs[] = '<li><a href="' . esc_url($office_link) . '">' . esc_html($cpt_label) . ': ' . esc_html($office->name) . '</a></li>';
            }
            $crumbs[] = '<li class="last">' . esc_html($title) . '</li>';
            $crumbs_html = implode('', $crumbs);
        } 
        elseif (is_tax($this->office_taxonomy)) {
            // Office taxonomy archive (e.g., office/term/) - show Home > CPT Label > Office Name
            $term = get_queried_object();
            if (!$term || is_wp_error($term)) return;
            $office_link = get_term_link($term);
            $home_url = esc_url(home_url('/'));
            // Try to get CPT label from first post in the loop
            global $wp_query;
            $cpt_label = '';
            if (!empty($wp_query->posts) && isset($wp_query->posts[0])) {
                $first_post = $wp_query->posts[0];
                $post_type = get_post_type($first_post);
                if ($post_type) {
                    $cpt_obj = get_post_type_object($post_type);
                    $cpt_label = $cpt_obj && isset($cpt_obj->labels->name) ? $cpt_obj->labels->name : '';
                }
            }
            // Fallback: get first CPT registered to this taxonomy
            if (!$cpt_label) {
                $tax = get_taxonomy($this->office_taxonomy);
                if ($tax && !empty($tax->object_type)) {
                    $cpt_obj = get_post_type_object($tax->object_type[0]);
                    $cpt_label = $cpt_obj && isset($cpt_obj->labels->name) ? $cpt_obj->labels->name : '';
                }
            }
            $crumbs = array();
            $crumbs[] = '<li><a href="' . $home_url . '">' . esc_html__('Home', 'cuny-cpt') . '</a></li>';
            $crumbs[] = '<li class="last">' . esc_html($cpt_label) . ': ' . esc_html($term->name) . '</li>';
            $crumbs_html = implode('', $crumbs);
        }
        elseif (is_post_type_archive()) {
            $cpt = get_post_type();
            if (!$cpt) {
                global $wp_query;
                $cpt = $wp_query->query_vars['post_type'] ?? '';
            }
            $cpt_obj = get_post_type_object($cpt);
            if (!$cpt_obj) return;
            $home_url = esc_url(home_url('/'));
            $cpt_label = $cpt_obj->labels->name;
            $crumbs = array();
            $crumbs[] = '<li><a href="' . $home_url . '">' . esc_html__('Home', 'cuny-cpt') . '</a></li>';
            $crumbs[] = '<li class="last">' . esc_html($cpt_label) . '</li>';
            $crumbs_html = implode('', $crumbs);
            // Change the page title to CPT archive name
            ?>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var h1 = document.querySelector('h1.page-title');
                if (h1) {
                    h1.textContent = <?php echo json_encode($cpt_label); ?>;
                }
            });
            </script>
            <?php
        } else {
            return;
        }
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var bcDiv = document.getElementById('breadcrumbs');
            if (bcDiv) {
                var ul = bcDiv.querySelector('ul.inline');
                if (ul) {
                    ul.innerHTML = <?php echo json_encode($crumbs_html); ?>;
                } else {
                    // fallback: add a new ul.inline
                    var newUl = document.createElement('ul');
                    newUl.className = 'inline';
                    newUl.innerHTML = <?php echo json_encode($crumbs_html); ?>;
                    bcDiv.appendChild(newUl);
                }
            }
        });
        </script>
        <?php
    }
}
