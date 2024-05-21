<?php
/**
 * Plugin Name: Tip My Tunes
 * Description: Allows users to add, edit, and delete a list of songs with PayPal payment integration.
 * Version: 7.0
 * Author: Brandon Slack
 */

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Buffer all output
ob_start();

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/admin-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/song-post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/song-request-post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/url-rewrites.php';
require_once plugin_dir_path(__FILE__) . 'includes/profile-fields.php';
require_once plugin_dir_path(__FILE__) . 'includes/settings-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax-handlers.php';
require_once plugin_dir_path(__FILE__) . 'includes/paypal-create-payment.php';
require_once plugin_dir_path(__FILE__) . 'includes/paypal-return-handler.php'; // Merged success and cancel handler
require_once plugin_dir_path(__FILE__) . 'includes/paypal-rewrite-rules.php';

// Enqueue styles and scripts
function tip_my_tunes_enqueue_assets() {
    wp_enqueue_style('tip-my-tunes-styles', plugin_dir_url(__FILE__) . 'includes/css/tip-my-tunes-styles.css');
    wp_enqueue_script('jquery'); // Enqueue jQuery
}
add_action('wp_enqueue_scripts', 'tip_my_tunes_enqueue_assets');

// Enqueue PayPal SDK
function enqueue_paypal_sdk() {
    $settings = get_option('tip_my_tunes_settings');
    $client_id = isset($settings['paypal_client_id']) ? esc_attr($settings['paypal_client_id']) : '';
    if ($client_id) {
        echo '<script src="https://www.paypal.com/sdk/js?client-id=' . $client_id . '"></script>';
    }
}
add_action('wp_head', 'enqueue_paypal_sdk');

// Hook to create user page on registration
add_action('user_register', 'create_user_page');

// Add capabilities to Subscriber role
function add_subscriber_caps() {
    $role = get_role('subscriber');
    $role->add_cap('edit_posts');
    $role->add_cap('edit_published_posts');
    $role->add_cap('publish_posts');
    $role->add_cap('delete_posts');
}
add_action('admin_init', 'add_subscriber_caps');

// Flush rewrite rules on activation
function tip_my_tunes_activate() {
    custom_rewrite_rules();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'tip_my_tunes_activate');

// Flush rewrite rules on deactivation
function tip_my_tunes_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'tip_my_tunes_deactivate');

// Create pages for payment success and cancel
function tip_my_tunes_create_pages() {
    if (!get_page_by_path('payment-success')) {
        wp_insert_post([
            'post_title' => 'Payment Success',
            'post_name' => 'payment-success',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => 'Thank you for your purchase!'
        ]);
    }
    if (!get_page_by_path('payment-cancel')) {
        wp_insert_post([
            'post_title' => 'Payment Cancel',
            'post_name' => 'payment-cancel',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => 'Your payment was cancelled.'
        ]);
    }
}
register_activation_hook(__FILE__, 'tip_my_tunes_create_pages');

// End output buffer
ob_end_clean();
?>
