<?php
// Add custom rewrite rules
function add_custom_rewrite_rules() {
    add_rewrite_rule('^payment-success/?', 'index.php?pagename=payment-success', 'top');
    add_rewrite_rule('^payment-cancel/?', 'index.php?pagename=payment-cancel', 'top');
    flush_rewrite_rules();
}

add_action('init', 'add_custom_rewrite_rules');

// Handle the return page template
function load_return_template($template) {
    if (get_query_var('pagename') == 'payment-success' || get_query_var('pagename') == 'payment-cancel') {
        return plugin_dir_path(__FILE__) . '../templates/payment-return.php';
    }
    return $template;
}

add_filter('template_include', 'load_return_template');
?>
