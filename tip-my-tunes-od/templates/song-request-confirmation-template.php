<?php
get_header();
$user = get_user_by('slug', get_query_var('author_name'));

if ($user) {
    $song_id = isset($_GET['song_id']) ? intval($_GET['song_id']) : '';
    $first_name = isset($_GET['first_name']) ? sanitize_text_field($_GET['first_name']) : '';
    $payer_email = isset($_GET['payer_email']) ? sanitize_text_field($_GET['payer_email']) : '';

    echo '<h1>Your Song has been added to the Song Queue</h1>';
    echo '<p>Thank you for your request. Your song has been added to the queue.</p>';
    echo '<a href="' . esc_url(site_url('/' . $user->user_nicename . '/songque')) . '" class="button">Go to Song Queue</a>';

    if ($song_id && $first_name && $payer_email) {
        // Add a new song request post with the PayPal first name and time submitted
        $post_data = array(
            'post_title' => get_the_title($song_id) . ' - ' . $first_name,
            'post_type' => 'song_request',
            'post_status' => 'publish',
            'post_author' => $user->ID,
            'meta_input' => array(
                'payer_email' => $payer_email,
                'time_submitted' => current_time('mysql')
            )
        );
        wp_insert_post($post_data);
    }
} else {
    echo '<p>User not found.</p>';
}

get_footer();
?>
