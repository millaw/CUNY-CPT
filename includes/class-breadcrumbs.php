<?php
class CUNY_CPT_Breadcrumbs {
    private $office_taxonomy;

    public function __construct($office_taxonomy = 'office') {
        $this->office_taxonomy = $office_taxonomy;
        add_filter('the_content', array($this, 'remove_breadcrumbs_from_content'));
        add_action('wp_footer', array($this, 'replace_theme_breadcrumbs_div'));
        add_action('pre_get_posts', array($this, 'filter_main_query_by_tax_and_cpt'));
    }

    public function remove_breadcrumbs_from_content($content) {
        return $content;
    }

    public function filter_main_query_by_tax_and_cpt($query) {
        if (!is_admin() && $query->is_main_query() && is_tax($this->office_taxonomy)) {
            $term = get_queried_object();
            if (!$term || is_wp_error($term)) return;

            $cpt_slug = '';

            // 1. If URL explicitly has post_type
            if (!empty($_GET['post_type'])) {
                $cpt_slug = sanitize_key($_GET['post_type']);
                setcookie('last_cpt_slug', $cpt_slug, time() + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
            }

            // 2. If not, try cookie
            elseif (!empty($_COOKIE['last_cpt_slug'])) {
                $cpt_slug = sanitize_key($_COOKIE['last_cpt_slug']);
            }

            // 3. If still not, fallback to first CPT for taxonomy
            if (!$cpt_slug) {
                $tax = get_taxonomy($this->office_taxonomy);
                if ($tax && !empty($tax->object_type)) {
                    $cpt_slug = $tax->object_type[0];
                }
            }

            if (!$cpt_slug) return;

            // Enforce CPT and taxonomy
            $query->set('post_type', [$cpt_slug]);
            $query->set('tax_query', [[
                'taxonomy' => $this->office_taxonomy,
                'field'    => 'slug',
                'terms'    => $term->slug,
            ]]);
        }
    }

    public function replace_theme_breadcrumbs_div() {
        $crumbs_html = '';
        $term = null;
        $cpt_slug = '';
        $cpt_label = '';

        if (is_singular()) {
            global $post;
            $cpt = get_post_type($post);

            // Store CPT in a cookie for later use
            setcookie('last_cpt_slug', $cpt, time() + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);

            $terms = get_the_terms($post, $this->office_taxonomy);
            if (!$terms || is_wp_error($terms)) return;
            $office = $terms[0];
            $office_link = get_term_link($office);
            if (is_wp_error($office_link)) return;
            $home_url = esc_url(home_url('/'));
            $cpt_obj = get_post_type_object($cpt);
            $cpt_label = $cpt_obj ? $cpt_obj->labels->name : ucfirst($cpt);
            $cpt_archive = get_post_type_archive_link($cpt);
            $title = get_the_title($post);

            $crumbs = array();
            $crumbs[] = '<li><a href="' . $home_url . '">' . esc_html__('Home', 'cuny-cpt') . '</a></li>';
            if ($cpt_archive) {
                $crumbs[] = '<li><a href="' . esc_url($cpt_archive) . '">' . esc_html($cpt_label) . '</a></li>';
            } else {
                $crumbs[] = '<li><a href="' . esc_url($office_link) . '">' . esc_html($cpt_label) . ': ' . esc_html($office->name) . '</a></li>';
            }
            $crumbs[] = '<li class="last">' . esc_html($title) . '</li>';
            $crumbs_html = implode('', $crumbs);
        }

        elseif (is_tax($this->office_taxonomy)) {
            $term = get_queried_object();
            if (!$term || is_wp_error($term)) return;
            $office_link = get_term_link($term);
            $home_url = esc_url(home_url('/'));
            $cpt_slug = '';
            $cpt_label = '';
            $referrer = isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : '';

            if ($referrer) {
                $ref_post_id = url_to_postid($referrer);
                if ($ref_post_id) {
                    $ref_post_type = get_post_type($ref_post_id);
                    $cpt_obj = get_post_type_object($ref_post_type);
                    $cpt_label = $cpt_obj && isset($cpt_obj->labels->name) ? $cpt_obj->labels->name : '';
                    $cpt_slug = $ref_post_type;
                }
            }

            if (!$cpt_label && !empty($GLOBALS['wp_query']->posts)) {
                $first_post = $GLOBALS['wp_query']->posts[0];
                $post_type = get_post_type($first_post);
                if ($post_type) {
                    $cpt_obj = get_post_type_object($post_type);
                    $cpt_label = $cpt_obj && isset($cpt_obj->labels->name) ? $cpt_obj->labels->name : '';
                    $cpt_slug = $post_type;
                }
            }

            if (!$cpt_label) {
                $tax = get_taxonomy($this->office_taxonomy);
                if ($tax && !empty($tax->object_type)) {
                    $cpt_obj = get_post_type_object($tax->object_type[0]);
                    $cpt_label = $cpt_obj && isset($cpt_obj->labels->name) ? $cpt_obj->labels->name : '';
                    $cpt_slug = $tax->object_type[0];
                }
            }

            $filtered_url = '';
            if ($cpt_slug && $term) {
                $cpt_archive = get_post_type_archive_link($cpt_slug);
                if ($cpt_archive) {
                    $filtered_url = add_query_arg($this->office_taxonomy, $term->slug, $cpt_archive);
                } else {
                    $filtered_url = add_query_arg(array('post_type' => $cpt_slug, $this->office_taxonomy => $term->slug), home_url('/'));
                }
            }

            $crumbs = array();
            $crumbs[] = '<li><a href="' . $home_url . '">' . esc_html__('Home', 'cuny-cpt') . '</a></li>';
            if ($cpt_label && $filtered_url) {
                // $crumbs[] = '<li><a href="' . esc_url($filtered_url) . '">' . esc_html($cpt_label) . ': ' . esc_html($term->name) . '</a></li>';
                $crumbs[] = '<li class="last">' . esc_html($cpt_label) . ': ' . esc_html($term->name) . '</li>';
            } elseif ($cpt_label) {
                $crumbs[] = '<li>' . esc_html($cpt_label) . ': ' . esc_html($term->name) . '</li>';
            }
            $crumbs_html = implode('', $crumbs);

            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var h1 = document.querySelector('h1.page-title');
                    if (h1) {
                        h1.textContent = " . json_encode(trim($cpt_label ? $cpt_label . ': ' . $term->name : $term->name)) . ";
                    }
                });
            </script>";
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

            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var h1 = document.querySelector('h1.page-title');
                    if (h1) {
                        h1.textContent = " . json_encode($cpt_label) . ";
                    }
                });
            </script>";
        } else {
            return;
        }

        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                var bcDiv = document.getElementById('breadcrumbs');
                if (bcDiv) {
                    var ul = bcDiv.querySelector('ul.inline');
                    if (ul) {
                        ul.innerHTML = " . json_encode($crumbs_html) . ";
                    } else {
                        var newUl = document.createElement('ul');
                        newUl.className = 'inline';
                        newUl.innerHTML = " . json_encode($crumbs_html) . ";
                        bcDiv.appendChild(newUl);
                    }
                }
            });
        </script>";
    }
}
