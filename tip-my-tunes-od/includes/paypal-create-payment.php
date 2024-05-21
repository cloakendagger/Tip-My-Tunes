<?php
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
    $redirectUrls->setReturnUrl(home_url('/payment-success?song_id=' . $song_id . '&user_id=' . $user_id))
                 ->setCancelUrl(home_url('/payment-cancel'));

    $payment = new \PayPal\Api\Payment();
    $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions([$transaction])
            ->setRedirectUrls($redirectUrls);

    // Log the payment payload
    error_log('PayPal Payment Payload: ' . print_r($payment->toArray(), true));

    try {
        $payment->create($apiContext);
        $response = ['url' => $payment->getApprovalLink()];
    } catch (PayPal\Exception\PayPalConnectionException $ex) {
        $response = ['error' => $ex->getMessage()];
        // Log the detailed error message from PayPal
        error_log('PayPal Payment Creation Error: ' . $ex->getData());
    } catch (Exception $e) {
        $response = ['error' => $e->getMessage()];
        error_log('PayPal Payment Creation Error: ' . $e->getMessage());
    }

    // Debugging: Log the response
    error_log(print_r($response, true));

    echo json_encode($response);

    wp_die();
}
add_action('wp_ajax_tip_my_tunes_create_payment', 'handle_paypal_payment');
add_action('wp_ajax_nopriv_tip_my_tunes_create_payment', 'handle_paypal_payment');
?>
