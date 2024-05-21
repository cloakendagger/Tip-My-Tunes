<?php
function handle_paypal_ipn() {
    // Check if this is a PayPal IPN request by looking for required IPN variables
    if (!isset($_POST['txn_id']) || !isset($_POST['receiver_email'])) {
        // This is not a PayPal IPN request, ignore it
        return;
    }

    // PayPal settings
    $options = get_option('tip_my_tunes_settings');
    $paypal_mode = isset($options['tip_my_tunes_mode']) && $options['tip_my_tunes_mode'] === 'live' ? '' : 'sandbox.';
    $paypal_url = 'https://www.' . $paypal_mode . 'paypal.com/cgi-bin/webscr';

    // Read POST data
    $raw_post_data = file_get_contents('php://input');
    $raw_post_array = explode('&', $raw_post_data);
    $myPost = array();
    foreach ($raw_post_array as $keyval) {
        $keyval = explode('=', $keyval);
        if (count($keyval) == 2) {
            $myPost[$keyval[0]] = urldecode($keyval[1]);
        }
    }

    // Log the raw POST data for debugging
    error_log('PayPal IPN Raw POST Data: ' . print_r($myPost, true));

    // Build the required acknowledgement message out of the notification just received
    $req = 'cmd=_notify-validate';
    foreach ($myPost as $key => $value) {
        $value = urlencode($value);
        $req .= "&$key=$value";
    }

    // Log the request being sent back to PayPal for verification
    error_log('PayPal IPN Verification Request: ' . $req);

    // Post the acknowledgement back to PayPal
    $ch = curl_init($paypal_url);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close', 'User-Agent: WordPress'));

    $res = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Get HTTP response code
    if (curl_errno($ch)) {
        $res .= ' - CURL error: ' . curl_error($ch);
    }
    curl_close($ch);

    // Log the IPN response for debugging
    error_log('PayPal IPN Response: ' . $res . ' - HTTP code: ' . $http_code);

    // Inspect IPN validation result and act accordingly
    if (strcmp($res, "VERIFIED") == 0) {
        // The IPN is verified, proceed with processing
        $payer_first_name = isset($myPost['first_name']) ? $myPost['first_name'] : '';
        $item_name = isset($myPost['item_name']) ? $myPost['item_name'] : '';
        $payer_email = isset($myPost['payer_email']) ? $myPost['payer_email'] : '';
        $receiver_email = isset($myPost['receiver_email']) ? $myPost['receiver_email'] : '';

        // Retrieve the user ID by matching the PayPal email
        $user_query = new WP_User_Query(array(
            'meta_key' => 'paypal_email',
            'meta_value' => $receiver_email,
            'number' => 1,
            'fields' => 'ID'
        ));

        $users = $user_query->get_results();
        if (!empty($users)) {
            $user_id = $users[0]; // Get the first matched user ID

            // Add a new song request post with the PayPal first name and time submitted
            $post_data = array(
                'post_title' => $item_name . ' - ' . $payer_first_name,
                'post_type' => 'song_request',
                'post_status' => 'publish',
                'post_author' => $user_id,
                'meta_input' => array(
                    'payer_email' => $payer_email,
                    'time_submitted' => current_time('mysql')
                )
            );
            wp_insert_post($post_data);
        } else {
            // Log if no matching user is found
            error_log('PayPal IPN: No user found with PayPal email: ' . $receiver_email);
        }
    } else {
        // Log for manual investigation
        error_log('PayPal IPN: Invalid IPN. Response: ' . $res . ' - HTTP code: ' . $http_code);
    }
}
add_action('init', 'handle_paypal_ipn');
?>
