<?php
// Include the PayPal SDK files
require_once plugin_dir_path(__FILE__) . 'paypal-sdk-includes.php';

function handle_paypal_payment() {
    // Log incoming POST data
    error_log('Incoming POST data: ' . print_r($_POST, true));

    if (!isset($_POST['song_id']) || !isset($_POST['user_id'])) {
        echo json_encode(['error' => 'Invalid request.']);
        wp_die();
    }

    $song_id = intval($_POST['song_id']);
    $user_id = intval($_POST['user_id']);
    $song_title = get_the_title($song_id);
    $song_price = get_post_meta($song_id, 'song_price', true);
    $paypal_email = get_the_author_meta('paypal_email', $user_id);

    // Log PayPal email
    error_log('PayPal email for user ' . $user_id . ': ' . $paypal_email);

    if (empty($paypal_email)) {
        echo json_encode(['error' => 'PayPal email is not set. Please update your profile with a valid PayPal email.']);
        wp_die();
    }

    $settings = get_option('tip_my_tunes_settings');
    $clientId = isset($settings['paypal_client_id']) ? trim($settings['paypal_client_id']) : '';
    $clientSecret = isset($settings['paypal_client_secret']) ? trim($settings['paypal_client_secret']) : '';

    // Log PayPal credentials (Remove this after debugging)
    error_log('PayPal Client ID: ' . $clientId);
    error_log('PayPal Client Secret: ' . $clientSecret);

    if (empty($clientId) || empty($clientSecret)) {
        echo json_encode(['error' => 'PayPal Client ID and/or Secret are not set. Please update the plugin settings.']);
        wp_die();
    }

    // Create a new instance of the PayPal API context
    $apiContext = new \PayPal\Rest\ApiContext(
        new \PayPal\Auth\OAuthTokenCredential(
            $clientId,
            $clientSecret
        )
    );

    // Create a new payment
    $payer = new \PayPal\Api\Payer();
    $payer->setPaymentMethod('paypal');

    $amount = new \PayPal\Api\Amount();
    $amount->setTotal($song_price);
    $amount->setCurrency('USD');

    $transaction = new \PayPal\Api\Transaction();
    $transaction->setAmount($amount);
    $transaction->setDescription('Payment for song request: ' . $song_title);

    // Pass custom data (song ID and user ID) for later use
    $transaction->setCustom($song_id . ',' . $user_id);

    $redirectUrls = new \PayPal\Api\RedirectUrls();
    $redirectUrls->setReturnUrl(home_url('/payment-success?paymentId={paymentId}&PayerID={PayerID}&song_id=' . $song_id . '&user_id=' . $user_id))
                 ->setCancelUrl(home_url('/payment-cancel'));

    $payment = new \PayPal\Api\Payment();
    $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions([$transaction])
            ->setRedirectUrls($redirectUrls);

    try {
        $payment->create($apiContext);
        $response = ['url' => $payment->getApprovalLink()];
    } catch (Exception $e) {
        $response = ['error' => $e->getMessage()];
    }

    // Debugging: Log the response
    error_log(print_r($response, true));

    echo json_encode($response);

    wp_die();
}
add_action('wp_ajax_tip_my_tunes_create_payment', 'handle_paypal_payment');
add_action('wp_ajax_nopriv_tip_my_tunes_create_payment', 'handle_paypal_payment');

// Handle PayPal return URL for successful payments
function handle_paypal_success() {
    // Get the payment ID and payer ID from the URL
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
            }
        } catch (Exception $e) {
            // Log the error and redirect to a cancel page
            error_log($e->getMessage());
            wp_redirect(home_url('/payment-cancel'));
            exit;
        }
    }
}

add_action('init', 'add_custom_rewrite_rules');
function add_custom_rewrite_rules() {
    add_rewrite_rule('^payment-success/?', 'index.php?pagename=payment-success', 'top');
    add_rewrite_rule('^payment-cancel/?', 'index.php?pagename=payment-cancel', 'top');
    flush_rewrite_rules();
}

// Handle the success page template
function load_success_template($template) {
    if (get_query_var('pagename') == 'payment-success') {
        return plugin_dir_path(__FILE__) . '../templates/payment-success.php';
    }
    return $template;
}

add_filter('template_include', 'load_success_template');

// Handle the cancel page template
function load_cancel_template($template) {
    if (get_query_var('pagename') == 'payment-cancel') {
        return plugin_dir_path(__FILE__) . '../templates/payment-cancel.php';
    }
    return $template;
}

add_filter('template_include', 'load_cancel_template');
?>
