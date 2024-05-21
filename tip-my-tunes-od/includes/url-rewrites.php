<?php
function custom_rewrite_rules() {
    add_rewrite_rule('^([^/]*)/songque/?$', 'index.php?author_name=$matches[1]&template=songque-template', 'top');
    add_rewrite_rule('^([^/]*)/song-request-confirmation/?$', 'index.php?author_name=$matches[1]&template=song-request-confirmation-template', 'top');
    add_rewrite_rule('^([^/]*)/songs/?$', 'index.php?author_name=$matches[1]&template=user-songs-template', 'top');
}
add_action('init', 'custom_rewrite_rules');

function add_query_vars($vars) {
    $vars[] = 'template';
    return $vars;
}
add_filter('query_vars', 'add_query_vars');

function load_custom_template($template) {
    global $wp_query;

    if (isset($wp_query->query_vars['template'])) {
        $custom_template = plugin_dir_path(__FILE__) . '../templates/' . $wp_query->query_vars['template'] . '.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }

    return $template;
}
add_filter('template_include', 'load_custom_template');
?>
