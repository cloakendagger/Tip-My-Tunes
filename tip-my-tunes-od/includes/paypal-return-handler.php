<?php
require_once plugin_dir_path(__FILE__) . 'paypal-sdk-includes.php';

function handle_paypal_return() {
    if (isset($_GET['paymentId']) && isset($_GET['PayerID'])) {
        $paymentId = sanitize_text_field($_GET['paymentId']);
        $payerId = sanitize_text_field($_GET['PayerID']);
        $song_id = intval($_GET['song_id']);
        $user_id = intval($_GET['user_id']);

        $settings = get_option('tip_my_tunes_settings');
        $clientId = isset($settings['paypal_client_id']) ? trim($settings['paypal_client_id']) : '';
        $clientSecret = isset($settings['paypal_client_secret']) ? trim($settings['paypal_client_secret']) : '';

        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $clientId,
                $clientSecret
            )
        );

        // Get the payment object by passing paymentId
        $payment = \PayPal\Api\Payment::get($paymentId, $apiContext);

        // Execute the payment
        $execution = new \PayPal\Api\PaymentExecution();
        $execution->setPayerId($payerId);

        try {
            $result = $payment->execute($execution, $apiContext);

            // If the payment is successful, add the song to the song queue
            if ($result->getState() == 'approved') {
                // Add song to the user's song queue
                $post_data = array(
                    'post_title'  => get_the_title($song_id),
                    'post_status' => 'publish',
                    'post_type'   => 'song_request',
                    'post_author' => $user_id,
                );
                $post_id = wp_insert_post($post_data);
                add_post_meta($post_id, 'time_submitted', current_time('mysql'));

                // Redirect to a success page with a success message
                wp_redirect(home_url('/payment-success?song_id=' . $song_id));
                exit;
            } else {
                // Redirect to a cancel page
                wp_redirect(home_url('/payment-cancel'));
                exit;
            }
        } catch (Exception $e) {
            // Log the error and redirect to a cancel page
            error_log($e->getMessage());
            wp_redirect(home_url('/payment-cancel'));
            exit;
        }
    } else {
        // Handle cases where paymentId or PayerID is not set (cancel cases)
        wp_redirect(home_url('/payment-cancel'));
        exit;
    }
}

add_action('init', 'add_custom_rewrite_rules');
?>
