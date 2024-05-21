<?php
get_header();
$user = wp_get_current_user();

if ($user) {
    echo '<h1>Request a Song</h1>';

    if (isset($_GET['song_id'])) {
        $song_id = intval($_GET['song_id']);
        $song_title = get_the_title($song_id);
        $song_price = get_post_meta($song_id, 'song_price', true);

        // Ensure the PayPal email is correctly retrieved from the user profile
        $paypal_email = get_the_author_meta('paypal_email', $user->ID);

        if (empty($paypal_email)) {
            echo '<p>PayPal email is not set. Please update your profile with a valid PayPal email.</p>';
        } else {
            $options = get_option('tip_my_tunes_settings');
            $paypal_mode = isset($options['tip_my_tunes_mode']) && $options['tip_my_tunes_mode'] === 'live' ? '' : '.sandbox';

            echo '<p>Song: ' . esc_html($song_title) . '</p>';
            echo '<p>Price: $' . esc_html($song_price) . '</p>';
            echo '<form action="https://www' . $paypal_mode . '.paypal.com/cgi-bin/webscr" method="post" target="_top">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="' . esc_attr($paypal_email) . '">
                <input type="hidden" name="item_name" value="' . esc_attr($song_title) . '">
                <input type="hidden" name="amount" value="' . esc_attr($song_price) . '">
                <input type="hidden" name="currency_code" value="USD">
                <input type="hidden" name="return" value="' . esc_url(site_url('/' . $user->user_nicename . '/song-request-confirmation?song_id=' . $song_id . '&first_name=' . urlencode($user->first_name) . '&payer_email=' . urlencode($paypal_email))) . '">
                <input type="hidden" name="cancel_return" value="' . esc_url(site_url('/paypal-cancel')) . '">
                <input type="hidden" name="notify_url" value="' . esc_url(site_url('/paypal-ipn')) . '">
                <input type="submit" value="Pay with PayPal" class="button">
            </form>';
        }
    } else {
        echo '<p>No song selected.</p>';
    }
} else {
    echo '<p>You need to log in to request a song.</p>';
}

get_footer();
?>
